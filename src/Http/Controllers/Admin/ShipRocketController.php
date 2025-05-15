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
    
   
   
}
