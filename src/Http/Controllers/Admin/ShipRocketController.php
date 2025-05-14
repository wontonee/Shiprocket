<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Core\Models\CoreConfig;
use Illuminate\Http\Request;
use Wontonee\Shiprocket\Sdk\Client\Client;
use Wontonee\Shiprocket\DataGrids\ShipmentDataGrid;
use Webkul\DataGrid\Exceptions\InvalidColumnTypeException;
use Wontonee\Shiprocket\Repositories\ShiprocketRepository;

class ShiprocketController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('shiprocket::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('shiprocket::admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        return view('shiprocket::admin.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id) {}

    /**
     * Show the Shiprocket settings form.
     */
    public function settings()
    {
        $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;
        $licenseKey  = optional(CoreConfig::where('code', 'shiprocket.license_key')->first())->value;
        return view('shiprocket::admin.shiprocket-settings', compact('apiUsername', 'apiPassword', 'licenseKey'));
    }

    /**
     * Save the Shiprocket settings.
     */
    public function saveSettings(Request $request)
    {
        // Check if this is a removal request (all fields empty)
        $isRemoval = empty($request->api_username) && 
                    empty($request->api_password) && 
                    empty($request->license_key);

        if ($isRemoval) {
            // Delete existing settings
            CoreConfig::where('code', 'shiprocket.api_username')->delete();
            CoreConfig::where('code', 'shiprocket.api_password')->delete();
            CoreConfig::where('code', 'shiprocket.license_key')->delete();

            return redirect()->back()->with('success', 'Shiprocket settings have been removed.');
        }

        // Regular save operation
        $request->validate([
            'api_username' => 'required|string',
            'api_password' => 'required|string',
            'license_key'  => 'required|string',
        ]);

        CoreConfig::updateOrCreate(
            ['code' => 'shiprocket.api_username'],
            ['value' => $request->api_username]
        );
        CoreConfig::updateOrCreate(
            ['code' => 'shiprocket.api_password'],
            ['value' => $request->api_password]
        );
        CoreConfig::updateOrCreate(
            ['code' => 'shiprocket.license_key'],
            ['value' => $request->license_key]
        );

        return redirect()->back()->with('success', 'Shiprocket settings updated successfully.');
    }

    public function shipment()
    {
        if (request()->ajax()) {
            try {
                // Create the repository
                $repository = new ShiprocketRepository();
                
                // Reset cache if requested
                if (request()->has('refresh')) {
                    $repository->resetCache();
                }
                
                // Check if shipments data is available
                $shipments = $repository->getShipmentsWithOrderDetails();
                
                // Log shipments collection for debugging
                \Log::info('Retrieved ' . $shipments->count() . ' shipments from API');
                
                // If no data, return a basic response with empty records
                if ($shipments->isEmpty()) {
                    $perPage = request()->get('per_page') ?: 10;
                    return [
                        'records' => [],
                        'meta' => [
                            'total' => 0,
                            'per_page' => $perPage,
                            'current_page' => 1,
                        ],
                        'mass_actions' => [],
                    ];
                }
                
                // Use the global function to ensure proper instantiation with repository
                $dataGrid = app(ShipmentDataGrid::class, ['repository' => $repository]);
                $result = $dataGrid->process();
                
                // Convert JsonResponse to array if needed
                if ($result instanceof \Illuminate\Http\JsonResponse) {
                    \Log::info('DataGrid returned JsonResponse, converting to array');
                    $result = json_decode($result->getContent(), true);
                }
                
                // Ensure $result has all required fields for DataGrid
                if (!is_array($result)) {
                    \Log::error('DataGrid process() did not return an array: ' . gettype($result));
                    $result = [
                        'records' => [],
                        'meta' => [
                            'total' => 0,
                            'per_page' => 10,
                            'current_page' => 1,
                        ],
                        'columns' => [],
                        'actions' => [],
                        'mass_actions' => [],
                    ];
                } else {
                    // Ensure all required keys exist in the result
                    if (!isset($result['columns']) || !is_array($result['columns'])) {
                        $result['columns'] = [];
                    }
                    if (!isset($result['actions']) || !is_array($result['actions'])) {
                        $result['actions'] = [];
                    }
                    if (!isset($result['mass_actions']) || !is_array($result['mass_actions'])) {
                        $result['mass_actions'] = [];
                    }
                }
                
                return $result;
            } catch (\Webkul\DataGrid\Exceptions\InvalidColumnTypeException $e) {
                // Handle specific column type errors
                \Log::error('Shiprocket DataGrid Column Type Error: ' . $e->getMessage());
                
                // Simple fallback data grid response
                $perPage = request()->get('per_page') ?: 10;
                
                return [
                    'records' => [],
                    'meta' => [
                        'total' => 0,
                        'per_page' => $perPage,
                        'current_page' => 1,
                    ],
                    'mass_actions' => [],
                ];
            } catch (\Exception $e) {
                // Log the full exception details for debugging
                \Log::error('Shiprocket DataGrid Error: ' . $e->getMessage());
                
                // Simple fallback data grid response
                $perPage = request()->get('per_page') ?: 10;
                
                return [
                    'records' => [],
                    'meta' => [
                        'total' => 0,
                        'per_page' => $perPage,
                        'current_page' => 1,
                    ],
                    'mass_actions' => [],
                ];
            }
        }

        return view('shiprocket::admin.shiprocket-shipment');
    }
    
    /**
     * Cancel a shipment in Shiprocket
     * 
     * @param int $id The Shiprocket shipment ID
     * @return \Illuminate\Http\Response
     */
    public function cancelShipment($id = null)
    {
        try {
            // Robust ID processing - handle various input types
            if (is_object($id)) {
                // For objects, try to get a string representation
                if (method_exists($id, '__toString')) {
                    $id = $id->__toString();
                } else {
                    // Log this issue but continue with a fallback
                    \Log::warning("Object ID without __toString method: " . json_encode($id));
                    $id = json_encode($id);
                }
            }
            
            // Process the ID and make sure it's a valid value
            $cleanId = trim(preg_replace('/[^0-9]/', '', (string)$id));
            
            // Make sure we have a non-empty ID
            if (empty($cleanId)) {
                session()->flash('error', 'Shipment ID is missing or invalid');
                return redirect()->route('admin.shiprocket.shipment');
            }
            
            // Get Shiprocket API credentials
            $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
            $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;

            if (!$apiUsername || !$apiPassword) {
                session()->flash('error', 'Shiprocket API credentials are missing');
                return redirect()->route('admin.shiprocket.shipment');
            }

            // Initialize Shiprocket client
            $client = new Client($apiUsername, $apiPassword);
            
            // For shipments, we need to find the associated order first and cancel that
            // The $id here is the Shiprocket shipment ID
            $repository = new ShiprocketRepository();
            $shipments = $repository->getShipmentsWithOrderDetails();
            
            // Find the matching shipment and get its shiprocket_order_id
            $shipment = $shipments->firstWhere('shiprocket_shipment_id', $cleanId);
            
            $orderIds = [];
            
            if (!$shipment || !isset($shipment['shiprocket_order_id'])) {
                // Try direct API call to get the shipment details
                try {
                    $shipmentDetails = $client->shipments->get($cleanId);
                    
                    if (isset($shipmentDetails['order_id'])) {
                        $orderIds = [$shipmentDetails['order_id']];
                    } else {
                        session()->flash('error', 'Failed to cancel shipment');
                        return redirect()->route('admin.shiprocket.shipment');
                    }
                } catch (\Exception $e) {
                    \Log::error("Error getting shipment details: " . $e->getMessage());
                    session()->flash('error', 'Failed to cancel shipment');
                    return redirect()->route('admin.shiprocket.shipment');
                }
            } else {
                // Extract order ID and ensure it's a string
                $shipRocketOrderId = $shipment['shiprocket_order_id'];
                if (is_object($shipRocketOrderId)) {
                    $shipRocketOrderId = $shipRocketOrderId->__toString();
                }
                
                // Make sure it's a clean value
                $cleanOrderId = trim(preg_replace('/[^0-9]/', '', (string)$shipRocketOrderId));
                $orderIds = [$cleanOrderId];
            }
            
            // Cancel the order, which will cancel associated shipments
            $response = $client->orders->cancelorder($orderIds);
            
            if (isset($response['message']) && (strpos($response['message'], 'cancelled successfully') !== false)) {
                session()->flash('success', 'Shipment cancelled successfully');
            } else {
                session()->flash('error', $response['message'] ?? 'Failed to cancel shipment');
            }
            
            return redirect()->route('admin.shiprocket.shipment');
        } catch (\Exception $e) {
            \Log::error("Exception during cancelShipment: " . $e->getMessage());
            session()->flash('error', $e->getMessage());
            return redirect()->route('admin.shiprocket.shipment');
        }
    }

    /**
     * API endpoint for getting shipment data including HTML for the cancel button
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShipmentData()
    {
        try {
            // Create the repository
            $repository = new ShiprocketRepository();
            
            // Check if shipments data is available
            $shipments = $repository->getShipmentsWithOrderDetails();
            
            // Process each shipment to include HTML for actions
            $processedShipments = $shipments->map(function($shipment) {
                // Convert to array if it's not already
                $shipmentArray = is_array($shipment) ? $shipment : (array)$shipment;
                
                // Process the shipment ID to ensure it's a clean string
                $shipmentId = null;
                if (isset($shipmentArray['shiprocket_shipment_id'])) {
                    // Handle various types properly
                    if (is_object($shipmentArray['shiprocket_shipment_id'])) {
                        // For objects, try to get a string representation
                        if (method_exists($shipmentArray['shiprocket_shipment_id'], '__toString')) {
                            $shipmentId = $shipmentArray['shiprocket_shipment_id']->__toString();
                        } else {
                            // Log this issue but don't halt execution
                            \Log::warning("Object ShipmentID without __toString method: " . json_encode($shipmentArray));
                            $shipmentId = json_encode($shipmentArray['shiprocket_shipment_id']);
                        }
                    } else {
                        // For arrays, scalars, or null values, cast to string
                        $shipmentId = (string)$shipmentArray['shiprocket_shipment_id'];
                    }
                }
                
                // Clean the ID to ensure it's only numbers
                $cleanId = $shipmentId ? preg_replace('/[^0-9]/', '', $shipmentId) : '';
                
                // Add the cancel button with proper HTML structure for Bagisto datagrid
                if (in_array(strtoupper($shipmentArray['status'] ?? ''), ['PENDING', 'READY TO SHIP', 'NEW', 'PROCESSING']) && !empty($cleanId)) {
                    $url = route('admin.shiprocket.shipment.cancel', ['id' => $cleanId]);
                    $shipmentArray['actions'] = [
                        [
                            'title' => 'Cancel',
                            'icon' => 'icon-cancel',
                            'method' => 'GET',
                            'url' => $url,
                            'attributes' => [
                                'class' => 'cancel-shipment-action',
                                'data-shipment-id' => $cleanId,
                                'href' => $url
                            ]
                        ]
                    ];
                }
                
                // Add a presentable cancel button for backwards compatibility
                $shipmentArray['cancel_button'] = in_array(strtoupper($shipmentArray['status'] ?? ''), ['PENDING', 'READY TO SHIP', 'NEW', 'PROCESSING']) && !empty($cleanId)
                    ? '<a href="' . route('admin.shiprocket.shipment.cancel', ['id' => $cleanId]) . '" class="cancel-shipment-action" data-shipment-id="' . $cleanId . '">Cancel</a>'
                    : '';
                
                return $shipmentArray;
            });
            
            return response()->json([
                'success' => true,
                'data' => $processedShipments,
                'count' => $processedShipments->count()
            ]);
        } catch (\Exception $e) {
            \Log::error("Error getting shipment data: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add debug info to logs and return to client
     * This is helpful for diagnosing issues with the extension
     */
    protected function debugInfo($message, $data = [])
    {
        // Log the debug info
        \Log::debug($message, $data);
        
        // Return the info for AJAX debugging
        if (request()->ajax() && request()->has('debug')) {
            return response()->json([
                'debug' => [
                    'message' => $message,
                    'data' => $data,
                    'timestamp' => now()->toDateTimeString()
                ]
            ]);
        }
        
        return null;
    }
}
