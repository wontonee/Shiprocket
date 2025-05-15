<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Wontonee\Shiprocket\DataGrids\ShipmentDataGrid;
use Wontonee\Shiprocket\Sdk\Client\Client;
use Webkul\Core\Models\CoreConfig;
use Wontonee\Shiprocket\Repositories\ShiprocketRepository;
use Wontonee\Shiprocket\Repositories\ShipmentRepository;

use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * API Password
     *
     * @var string
     */
    protected $apiPassword;

    /**
     * API Username
     *
     * @var string
     */
    protected $apiUsername;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $this->apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;
    }

    public function shipment()
    {
        if (request()->ajax()) {
            try {
                // Create the repository
                $repository = new ShiprocketRepository();
                
                //new ShiprocketRepository();
                // new ShipmentRepository();
                // 

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

        return view('shiprocket::admin.shipment.index');
    }
}
