<?php

return [
    [
        'key'   => 'shiprocket',
        'name'  => 'Shiprocket',
        'route' => 'admin.shiprocket.shipment',
        'sort'  => 2,
        'children' => [
            [
                'key'        => 'shiprocket.shipment',
                'name'       => 'shiprocket::app.admin.acl.shipment',
                'route'      => 'admin.shiprocket.shipment',
                'sort'  => 1,
                'children'  => [
                    [
                        'key'        => 'shiprocket.shipment.view',
                        'name'       => 'shiprocket::app.admin.acl.shipment.view',
                        'route'      => 'admin.shiprocket.orders.view',
                        'sort'  => 1
                    ]
                ]
            ],
            [
                'key'        => 'shiprocket.courier',
                'name'       => 'shiprocket::app.admin.acl.courier',
                'route'      => 'admin.shiprocket.shipment',
                'sort'  => 2
            ],
            [
                'key'        => 'shiprocket.channel',
                'name'       => 'shiprocket::app.admin.acl.channel',
                'route'      => 'admin.shiprocket.channel',
                'sort'  => 3
            ],
            [
                'key'        => 'shiprocket.pickup',
                'name'       => 'shiprocket::app.admin.acl.pickup',
                'route'      => 'admin.shiprocket.pickup',
                'sort'  => 4
            ],
            [
                'key'        => 'shiprocket.settings',
                'name'       => 'shiprocket::app.admin.acl.settings',
                'route'      => 'admin.shiprocket.settings',
                'sort'  => 5
            ]
        ]
    ]
];