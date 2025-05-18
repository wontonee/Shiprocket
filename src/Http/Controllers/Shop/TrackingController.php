<?php

namespace Wontonee\Shiprocket\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Wontonee\Shiprocket\Sdk\Client\Client;
use Webkul\Core\Models\CoreConfig;


class TrackingController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

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
     * Display the tracking page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('shiprocket::shop.tracking.index');
    }

    /**
     * Display the tracking page with a specific shipment ID or awb number
     */
    public function show(Request $request)
    {
        // check if API credentials are set
        if (!$this->apiUsername || !$this->apiPassword) {
            return redirect()->route('shiprocket.shop.tracking.index')
                ->with('error', 'Shiprocket API credentials are not configured.');
        }

        $client = new Client($this->apiUsername, $this->apiPassword);

        $trackingby = $request->input('trackingby');
        $awbNumber = $request->input('awb_number');

        // Validate the inputs
        $validated = $request->validate([
            'trackingby' => ['required', 'integer', 'in:1,2'],
            'awb_number' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\-]+$/'],
        ], [
            'trackingby.required' => 'Tracking method is required.',
            'trackingby.integer' => 'Tracking method must be an integer.',
            'trackingby.in' => 'Invalid tracking method selected.',
            'awb_number.required' => 'AWB number is required.',
            'awb_number.string' => 'AWB number must be a string.',
            'awb_number.max' => 'AWB number cannot exceed 255 characters.',
            'awb_number.regex' => 'AWB number may only contain letters, numbers, and dashes.',
        ]);

        try {
            switch ((int)$trackingby) {
                case 1: // Shipment ID
                    $response = $client->tracking->fetchByShipmentId($awbNumber);
                    break;
                case 2: // AWB Number
                    $response = $client->tracking->fetchByAwb($awbNumber);
                    break;
                default:
                    return redirect()->route('shiprocket.shop.tracking.index')
                        ->with('error', 'Invalid tracking method selected.');
            }

            // Check for API errors or empty response
            if (empty($response) || (isset($response['status']) && $response['status'] === 'error')) {
                $errorMsg = isset($response['message']) ? $response['message'] : 'Unable to fetch tracking details.';
                return redirect()->route('shiprocket.shop.tracking.index')
                    ->with('error', $errorMsg);
            }
        } catch (\Exception $e) {
            return redirect()->route('shiprocket.shop.tracking.index')
                ->with('error', 'Error fetching tracking details: ' . $e->getMessage());
        }

        // Pass the response to the view for details
        return view('shiprocket::shop.tracking.show', [
            'trackingby' => $trackingby,
            'awbNumber' => $awbNumber,
            'response' => $response,
        ]);
    }
}
