<?php

return [
    'enabled' => env('SHIPROCKET_ENABLED', false),
    'api_key' => env('SHIPROCKET_API_KEY', ''),
    'api_secret' => env('SHIPROCKET_API_SECRET', ''),
    'pickup_location_id' => env('SHIPROCKET_PICKUP_LOCATION_ID', ''),
    'license_key' => env('SHIPROCKET_LICENSE_KEY', ''),
];
