<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Wontonee\Shiprocket\DataGrids\ShipmentDataGrid;
use Webkul\Core\Models\CoreConfig;
use Illuminate\Http\Request;

class SettingsController extends Controller
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
     * Show the Shiprocket settings form.
     */
    public function settings()
    {
        $apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;
        $licenseKey  = optional(CoreConfig::where('code', 'shiprocket.license_key')->first())->value;
        return view('shiprocket::admin.settings.index', compact('apiUsername', 'apiPassword', 'licenseKey'));
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

        // License key validation

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
}
