<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderAddressRepository;
use Wontonee\Shiprocket\Models\ShiprocketOrder;
use Wontonee\Shiprocket\Sdk\Client\Client;
use Webkul\Core\Models\CoreConfig;


class OrderController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * OrderRepository instance
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderAddressRepository instance
     *
     * @var \Webkul\Sales\Repositories\OrderAddressRepository
     */
    protected $orderAddressRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderAddressRepository  $orderAddressRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderAddressRepository $orderAddressRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderAddressRepository = $orderAddressRepository;
    }

    /**
     * Create a new shiprocket order from Bagisto order
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function create($orderId)
    {


        $order = $this->orderRepository->findOrFail($orderId);

        // Check if already sent to Shiprocket
        if (ShiprocketOrder::where('order_id', $orderId)->exists()) {
            session()->flash('error', __('shiprocket::app.admin.orders.already-sent-error'));
            return redirect()->back();
        }

        // Get Shiprocket API credentials
        $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;

        if (!$apiUsername || !$apiPassword) {
            session()->flash('error', __('shiprocket::app.admin.orders.missing-credentials'));
            return redirect()->back();
        }

        // Initialize Shiprocket client
        $client = new Client($apiUsername, $apiPassword);

        // Prepare order data for Shiprocket
        $orderData = $this->prepareOrderData($order);

        // For debugging - dump the order data to the screen
        // Uncomment the lines below for debugging
        /*
        echo '<h2>Debug: Order Data Being Sent to Shiprocket</h2>';
        echo '<pre>' . json_encode($orderData, JSON_PRETTY_PRINT) . '</pre>';
        
        // Show the token being used
        echo '<h2>API Token Used:</h2>';
        echo '<pre>' . $client->getToken() . '</pre>';
        
        exit; // Stop execution here for debugging
        */

        // Create order in Shiprocket
        $response = $client->orders->createadhocorder($orderData);

        if (isset($response['order_id'])) {
            // Create local record of Shiprocket order
            ShiprocketOrder::create([
                'order_id' => $orderId,
                'shiprocket_order_id' => $response['order_id'],
                'status' => $response['status'] ?? 'NEW',
            ]);


            session()->flash('success', __('shiprocket::app.admin.orders.create-success'));
        } else {
            // Format detailed error message if available
            $errorMessage = $response['message'] ?? 'Unknown error';

            // Check if there are field-specific errors
            if (isset($response['errors']) && is_array($response['errors'])) {
                $errorMessage .= '<br><br><strong>Error Details:</strong><br>';
                foreach ($response['errors'] as $field => $messages) {
                    if (is_array($messages)) {
                        foreach ($messages as $message) {
                            $errorMessage .= "- {$field}: {$message}<br>";
                        }
                    } else {
                        $errorMessage .= "- {$field}: {$messages}<br>";
                    }
                }
            }

            // Log the error response with detailed information
            \Log::error('Shiprocket Order Creation Failed', [
                'order_id' => $orderId,
                'response' => $response,
                'request_data' => $orderData
            ]);

            session()->flash('error', __('shiprocket::app.admin.orders.create-error', [
                'message' => $errorMessage
            ]));
        }

        return redirect()->back();
    }

    /**
     * View shiprocket order details
     *
     * @param  int  $orderId
     * @return \Illuminate\View\View
     */
    public function view($orderId)
    {
        // First try to find the order by Bagisto order_id
        $shiprocketOrder = ShiprocketOrder::where('order_id', $orderId)->first();

        // If not found, check if it's a Shiprocket order ID
        if (!$shiprocketOrder) {
            $shiprocketOrder = ShiprocketOrder::where('shiprocket_order_id', $orderId)->first();
        }

        // If still not found, show error
        if (!$shiprocketOrder) {
            session()->flash('error', __('shiprocket::app.admin.orders.not-found'));
            return redirect()->back();
        }

        // Get the Bagisto order using the order_id from ShiprocketOrder
        $order = $this->orderRepository->findOrFail($shiprocketOrder->order_id);

        // Get Shiprocket API credentials
        $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;

        // Initialize Shiprocket client
        $client = new Client($apiUsername, $apiPassword);

        // Fetch order details from Shiprocket
        $shiprocketOrderDetails = $client->orders->fetchcustomorder($shiprocketOrder->shiprocket_order_id);

        return view('shiprocket::admin.sales.orders.view', compact('order', 'shiprocketOrder', 'shiprocketOrderDetails'));
    }

    /**
     * Cancel an existing order in Shiprocket
     *
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function cancelOrder($orderId)
    {
        try {
            // Find the local Shiprocket order
            $shiprocketOrder = ShiprocketOrder::where('order_id', $orderId)->firstOrFail();

            // Get Shiprocket API credentials
            $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
            $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;

            if (!$apiUsername || !$apiPassword) {
                session()->flash('error', __('shiprocket::app.admin.orders.missing-credentials'));
                return redirect()->back();
            }

            // Initialize Shiprocket client
            $client = new Client($apiUsername, $apiPassword);

            // Ensure we have a clean order ID (convert objects to strings if needed)
            $shipRocketOrderId = is_object($shiprocketOrder->shiprocket_order_id)
                ? $shiprocketOrder->shiprocket_order_id->__toString()
                : $shiprocketOrder->shiprocket_order_id;

            // Make sure it's a clean value - ensure it's a string and has only numeric characters
            $cleanOrderId = trim(preg_replace('/[^0-9]/', '', (string)$shipRocketOrderId));

            // Log what we're doing to help with debugging
            //   \Log::info("Cancelling order with ID: {$cleanOrderId} (original: " . json_encode($shiprocketOrder->shiprocket_order_id) . ")");

            // Prepare order ID for cancellation - the SDK expects an array of IDs directly
            $orderIds = [$cleanOrderId];

            // Call the cancelorder method from the SDK
            $response = $client->orders->cancelorder($orderIds);


            if ($response['status_code'] == 200) {
                if (isset($response['is_duplicate_request']) && $response['message'] == 'Order cancelled successfully.') {
                    $shiprocketOrder->status = 'CANCELED';
                    $shiprocketOrder->update();
                    session()->flash('error', __('shiprocket::app.admin.orders.duplicate-request'));
                    return redirect()->back();
                } else {
                    // Update local order status
                    $shiprocketOrder->status = 'CANCELED';
                    $shiprocketOrder->update();
                    session()->flash('success', __('shiprocket::app.admin.orders.cancel-success'));
                }
            } else {
                session()->flash('error', $response['message'] ?? __('shiprocket::app.admin.orders.cancel-error'));
            }

            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Prepare order data for Shiprocket API
     *
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return array
     */
    protected function prepareOrderData($order)
    {
        $shippingAddress = $order->shipping_address;
        $billingAddress = $order->billing_address;

        // Handle case when there is no shipping address
        if (!$shippingAddress) {
            $shippingAddress = $billingAddress;
        }

        // Split the full name into first and last name
        $billingNameParts = $this->splitName($billingAddress->name);
        $shippingNameParts = $this->splitName($shippingAddress->name);

        // Prepare item data
        $items = [];
        $totalWeight = 0;

        foreach ($order->items as $item) {
            // Skip if this is not a physical product
            if ($item->type == 'virtual' || $item->type == 'downloadable') {
                continue;
            }

            // Get product weight or use default
            $weightPerUnit = 0.5; // Default weight in kg if not available
            if ($item->product && $item->product->weight) {
                $weightPerUnit = $item->product->weight;
            }

            $totalWeight += ($weightPerUnit * $item->qty_ordered);

            $items[] = [
                'name' => $item->name,
                'sku' => $item->sku,
                'units' => (int)$item->qty_ordered,
                'selling_price' => (string)number_format($item->price, 2, '.', ''),
                'discount' => (string)number_format($item->discount_amount, 2, '.', ''),
                'tax' => (string)number_format($item->tax_amount, 2, '.', ''),
                'hsn' => $item->product && isset($item->product->hsn_code) ? $item->product->hsn_code : '',
            ];
        }

        // If no items or total weight is 0, set a minimum weight
        if (empty($items) || $totalWeight <= 0) {
            $totalWeight = 0.5; // Minimum weight in kg
        }

        // Ensure we have a valid payment method for Shiprocket
        $paymentMethod = 'Prepaid';
        if ($order->payment && $order->payment->method) {
            // Map Bagisto payment methods to Shiprocket payment methods
            if (
                strpos($order->payment->method, 'cashon') !== false ||
                strpos($order->payment->method, 'cod') !== false
            ) {
                $paymentMethod = 'COD';
            }
        }

        // Format address data for Shiprocket
        // Ensure postcode is valid (numbers only)
        $billingPostcode = preg_replace('/[^0-9]/', '', $billingAddress->postcode);
        if (empty($billingPostcode)) {
            $billingPostcode = '110001'; // Default pincode if empty
        }

        $shippingPostcode = preg_replace('/[^0-9]/', '', $shippingAddress->postcode);
        if (empty($shippingPostcode)) {
            $shippingPostcode = '110001'; // Default pincode if empty
        }

        // Make sure billing and shipping addresses are not empty
        $billingAddressLine = trim($billingAddress->address1);
        if (empty($billingAddressLine)) {
            $billingAddressLine = 'Default Billing Address, New Delhi';
        }

        $billingAddress2 = trim($billingAddress->address2);

        $shippingAddressLine = trim($shippingAddress->address1);
        if (empty($shippingAddressLine)) {
            $shippingAddressLine = 'Default Shipping Address, New Delhi';
        }

        $shippingAddress2 = trim($shippingAddress->address2);

        // Default city, state and country if empty
        $billingCity = $billingAddress->city;
        if (empty($billingCity)) {
            $billingCity = 'New Delhi';
        }

        $billingState = $billingAddress->state;
        if (empty($billingState)) {
            $billingState = 'Delhi';
        }

        $billingCountry = $billingAddress->country;
        if (empty($billingCountry)) {
            $billingCountry = 'India';
        }

        $shippingCity = $shippingAddress->city;
        if (empty($shippingCity)) {
            $shippingCity = 'New Delhi';
        }

        $shippingState = $shippingAddress->state;
        if (empty($shippingState)) {
            $shippingState = 'Delhi';
        }

        $shippingCountry = $shippingAddress->country;
        if (empty($shippingCountry)) {
            $shippingCountry = 'India';
        }

        // Check if shipping is same as billing
        $shippingIsBilling = ($shippingAddress->id == $billingAddress->id);

        // Format phone numbers to ensure exactly 10 digits
        $billingPhone = preg_replace('/[^0-9]/', '', $billingAddress->phone);
        // Ensure it's exactly 10 digits - truncate or pad as needed
        if (strlen($billingPhone) > 10) {
            $billingPhone = substr($billingPhone, -10); // Take last 10 digits if longer
        } elseif (strlen($billingPhone) < 10) {
            $billingPhone = str_pad($billingPhone, 10, '9', STR_PAD_LEFT); // Pad with 9's if shorter
        }

        $shippingPhone = preg_replace('/[^0-9]/', '', $shippingAddress->phone);
        // Ensure it's exactly 10 digits - truncate or pad as needed
        if (strlen($shippingPhone) > 10) {
            $shippingPhone = substr($shippingPhone, -10); // Take last 10 digits if longer
        } elseif (strlen($shippingPhone) < 10) {
            $shippingPhone = str_pad($shippingPhone, 10, '9', STR_PAD_LEFT); // Pad with 9's if shorter
        }

        // If still empty, use a default 10-digit number
        if (empty($billingPhone)) {
            $billingPhone = '9999999999';
        }

        if (empty($shippingPhone)) {
            $shippingPhone = '9999999999';
        }

        // Get the current channel ID from core settings

        $currentChannelId = CoreConfig::where('code', 'shiprocket.shipping.channel_id')->first();

        if (!empty($currentChannelId)) {
            $currentChannelId = (string) $currentChannelId->value;
        } else {
            session()->flash('error', __('shiprocket::app.admin.orders.channel-missing'));
            return redirect()->back();
        }

        // check the pickup location
        $pickupLocation = CoreConfig::where('code', 'shiprocket.shipping.pickup_location_name')->first();
        if (!empty($pickupLocation)) {
            $pickupLocation = (string) $pickupLocation->value;
        } else {
            session()->flash('error', __('shiprocket::app.admin.orders.pickup-location-missing'));
            return redirect()->back();
        }



        // Prepare order data for Shiprocket according to their API documentation
        $orderData = [
            'order_id' => (string)$order->increment_id,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'pickup_location' => 'work',
            'channel_id' => $currentChannelId,
            'comment' => 'Order from ' . config('app.name'),
            'billing_customer_name' => $billingNameParts['first_name'],
            'billing_last_name' => $billingNameParts['last_name'],
            'billing_address' => $billingAddressLine,
            'billing_address_2' => $billingAddress2,
            'billing_city' => $billingCity,
            'billing_pincode' => $billingPostcode,
            'billing_state' => $billingState,
            'billing_country' => $billingCountry,
            'billing_email' => $billingAddress->email ?: 'customer@example.com',
            'billing_phone' => $billingPhone,
        ];

        if ($shippingIsBilling) {
            // When shipping is same as billing
            $orderData['shipping_is_billing'] = true;
        } else {
            // When shipping is different from billing
            $orderData['shipping_is_billing'] = false;
            $orderData['shipping_customer_name'] = $shippingNameParts['first_name'];
            $orderData['shipping_last_name'] = $shippingNameParts['last_name'];
            $orderData['shipping_address'] = $shippingAddressLine;
            $orderData['shipping_address_2'] = $shippingAddress2;
            $orderData['shipping_city'] = $shippingCity;
            $orderData['shipping_pincode'] = $shippingPostcode;
            $orderData['shipping_state'] = $shippingState;
            $orderData['shipping_country'] = $shippingCountry;
            $orderData['shipping_email'] = $shippingAddress->email ?: 'customer@example.com';
            $orderData['shipping_phone'] = $shippingPhone;
        }

        // Add remaining required fields
        $orderData['order_items'] = $items;
        $orderData['payment_method'] = $paymentMethod;
        $orderData['shipping_charges'] = number_format($order->shipping_amount, 2, '.', '');
        $orderData['giftwrap_charges'] = 0;
        $orderData['transaction_charges'] = 0;
        $orderData['total_discount'] = number_format($order->discount_amount, 2, '.', '');
        $orderData['sub_total'] = number_format($order->sub_total, 2, '.', '');
        $orderData['length'] = 10;
        $orderData['breadth'] = 15;
        $orderData['height'] = 20;
        $orderData['weight'] = number_format($totalWeight, 2, '.', '');

        return $orderData;
    }

    /**
     * Split a full name into first name and last name parts
     *
     * @param string $fullName
     * @return array
     */
    protected function splitName($fullName)
    {
        $parts = explode(' ', trim($fullName), 2);

        return [
            'first_name' => $parts[0],
            'last_name' => isset($parts[1]) ? $parts[1] : ''
        ];
    }
}
