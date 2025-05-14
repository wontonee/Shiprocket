<?php

return [
    [
        'key'   => 'shiprocket',
        'name'  => 'shiprocket::app.admin.menu.shiprocket',
        'route' => 'admin.shiprocket.index',
        'sort'  => 2,
        'icon'  => 'icon-rocket',
    ],
    [
        'key'        => 'shiprocket.shipment',
        'name'       => 'shiprocket::app.admin.menu.shipment',
        'route'      => 'admin.shiprocket.shipment',
        'sort'       => 1,
        'icon' => '',
    ],
      [ 
        'key'        => 'shiprocket.channel',
        'name'       => 'shiprocket::app.admin.menu.channel',
        'route'      => 'admin.shiprocket.channel',
        'sort'       => 2,
        'icon' => '',
      ],
      [
        'key'        => 'shiprocket.pickup',
        'name'       => 'shiprocket::app.admin.menu.pickup',
        'route'      => 'admin.shiprocket.pickup',
        'sort'       => 3,
        'icon' => '',
      ],
     [
        'key'        => 'shiprocket.settings',
        'name'       => 'shiprocket::app.admin.menu.settings',
        'route'      => 'admin.shiprocket.settings',
        'sort'       => 4,
        'icon' => '',
    ]
];