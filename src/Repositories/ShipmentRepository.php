<?php

namespace Wontonee\Shiprocket\Repositories;

use Wontonee\Shiprocket\Sdk\Client\Client;
use Webkul\Core\Models\CoreConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class ShipmentRepository
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->initClient();
    }

    /**
     * Initialize the Shiprocket client with credentials from settings
     */
    protected function initClient()
    {
        $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;

        if (!$apiUsername || !$apiPassword) {
            Log::error('Shiprocket API credentials are missing');
            return;
        }

        try {
            $this->client = new Client($apiUsername, $apiPassword);
        } catch (\Exception $e) {
            Log::error('Error initializing Shiprocket client: ' . $e->getMessage());
        }
    }

    /**
     * Get all shipments from Shiprocket
     *
     * @return Collection
     */
    public function getAllShipments(): Collection
    {
        if (!$this->client) {
            return collect([]);
        }

        try {
            // Use the SDK to fetch shipments
            $response = $this->client->shipment->fetchshipment();

            if (isset($response['error'])) {
                Log::error('Error fetching shipments from Shiprocket: ' . ($response['message'] ?? 'Unknown error'));
                return collect([]);
            }

            // Convert the response to a collection
            return collect($response['data'] ?? []);
        } catch (\Exception $e) {
            Log::error('Exception when fetching shipments from Shiprocket: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get shipment details along with related Bagisto order information
     *
     * @return Collection
     */
    public function getShipmentsWithOrderDetails(): Collection
    {
        $shipments = $this->getAllShipments();

        if ($shipments->isEmpty()) {
            return collect([]);
        }

        // Process shipments to include order information if available
        return $shipments->map(function ($shipment) {
            $orderDetails = $this->getRelatedOrderDetails($shipment['order_id'] ?? null);

            return [
                'id' => $shipment['id'] ?? null,
                'shiprocket_order_id' => $shipment['order_id'] ?? null,
                'shiprocket_shipment_id' => $shipment['shipment_id'] ?? null,
                'order_id' => $orderDetails['order_id'] ?? null,
                'status' => $shipment['status'] ?? null,
                'tracking_number' => $shipment['awb_code'] ?? null,
                'courier_name' => $shipment['courier_name'] ?? null,
                'awb_code' => $shipment['awb_code'] ?? null,
                'created_at' => $shipment['created_at'] ?? null,
                'updated_at' => $shipment['updated_at'] ?? null,
                'customer_first_name' => $orderDetails['customer_first_name'] ?? '',
                'customer_last_name' => $orderDetails['customer_last_name'] ?? '',
                'grand_total' => $orderDetails['grand_total'] ?? 0,
                'increment_id' => $orderDetails['increment_id'] ?? ''
            ];
        });
    }

    /**
     * Get related Bagisto order details for a Shiprocket order
     *
     * @param string|int|null $shiprocketOrderId
     * @return array
     */
    protected function getRelatedOrderDetails($shiprocketOrderId): array
    {
        if (!$shiprocketOrderId) {
            return [];
        }

        // Try to find the relationship in our local database first
        $shiprocketOrder = \Wontonee\Shiprocket\Models\ShiprocketOrder::where('shiprocket_order_id', $shiprocketOrderId)
            ->first();

        if (!$shiprocketOrder) {
            return [];
        }

        // Get order details
        $order = $shiprocketOrder->order;
        if (!$order) {
            return [];
        }

        return [
            'order_id' => $order->id,
            'customer_first_name' => $order->customer_first_name,
            'customer_last_name' => $order->customer_last_name,
            'grand_total' => $order->grand_total,
            'increment_id' => $order->increment_id
        ];
    }
}
