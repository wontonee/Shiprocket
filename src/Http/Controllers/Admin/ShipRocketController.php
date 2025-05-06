<?php

namespace Wontonee\ShipRocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Webkul\Core\Models\CoreConfig;

class ShipRocketController extends Controller
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
    public function destroy(int $id)
    {
        
    }

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

    /**
     * Show the Shiprocket shipment page.
     */
    public function shipment()
    {
        return view('shiprocket::admin.shipment');
    }
}
