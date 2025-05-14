<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Webkul\Core\Models\CoreConfig;
use Wontonee\Shiprocket\Sdk\Client\Client;

class PickupController extends Controller
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

    /**
     * Display the pickup page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (!$this->apiUsername || !$this->apiPassword) {
            session()->flash('error', 'Shiprocket API credentials are not configured.');
            return redirect()->route('admin.shiprocket.settings');
        }

        $client = new Client($this->apiUsername, $this->apiPassword);
        $response = $client->pickup->fetchPickup();

        $pickupLocations = [];

        if (isset($response['data']['shipping_address']) && is_array($response['data']['shipping_address'])) {
            foreach ($response['data']['shipping_address'] as $address) {
                $pickupLocations[] = [
                    'id' => $address['id'],
                    'name' => $address['pickup_location'],
                    'full_address' => $address['address'] . ', ' . $address['address_2'] . ', ' .
                        $address['city'] . ', ' . $address['state'] . ', ' .
                        $address['country'] . ' - ' . $address['pin_code'],
                    'contact_name' => $address['name'],
                    'contact_email' => $address['email'],
                    'contact_phone' => $address['phone'],
                    'is_primary' => $address['is_primary_location'] ?? 0
                ];
            }
        }

        $currentPickupId =  CoreConfig::where('code', 'shiprocket.shipping.pickup_location_id')->first();
        $currentPickupId = (int) $currentPickupId->value;

        return view('shiprocket::admin.shiprocket-pickup', [
            'apiConfigured' => true,
            'pickupLocations' => $pickupLocations,
            'currentPickupId' => $currentPickupId
        ]);
    }

    /**
     * Save pickup location to core settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function savePickupLocation(Request $request)
    {
        $request->validate([
            'pickup_location_id' => 'required|numeric'
        ]);

        $pickupLocationId = $request->input('pickup_location_id');

        // Save to core config
        $coreConfig = CoreConfig::updateOrCreate(
            [
                'code' => 'shiprocket.shipping.pickup_location_id'
            ],
            [
                'value' => $pickupLocationId
            ]
        );

        session()->flash('success', 'Pickup location has been saved successfully.');

        return redirect()->route('admin.shiprocket.pickup');
    }
}
