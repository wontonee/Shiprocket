<?php

return [
    [
        'key'    => 'shiprocket',
        'name'   => 'ShipRocket',
        'route'  => 'admin.shiprocket.index', // Ensure this route exists
        'sort'   => 3,
        'icon'   => 'icon-shipment', // Icon representing a rocket (symbolizing fast shipping)
    ],
    [
        'key'    => 'shiprocket.settings',
        'name'   => 'Settings',
        'route'  => 'admin.shiprocket.settings', // Verify this route exists
        'sort'   => 1,
        'icon'   => 'icon-settings', // Standard settings icon
        'parent' => 'shiprocket',
    ],
    [
        'key'    => 'shiprocket.shipment',
        'name'   => 'Shipment',
        'route'  => 'admin.shiprocket.shipment', // Verify this route exists
        'sort'   => 2,
        'icon'   => 'icon-truck', // Truck icon indicating shipment/delivery
        'parent' => 'shiprocket',
    ],
];
