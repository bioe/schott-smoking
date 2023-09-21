<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DEFINE MENU
    |--------------------------------------------------------------------------
    */

    'items' => [
        [
            'title' => 'Dashboard',
            'route' => 'dashboard'
        ],
        [
            'title' => 'Masterfile',
            'submenus' => [
                [
                    'title' => 'Users',
                    'route' => 'users.index'
                ],
                [
                    'title' => 'Stations',
                    'route' => 'stations.index'
                ],
            ]
        ],
        [
            'title' => 'HR',
            'submenus' => [
                [
                    'title' => 'Employees',
                    'route' => 'employees.index'
                ],
                [
                    'title' => 'Cost Center',
                    'route' => 'costcenters.index'
                ],
            ]
        ],
        [
            'title' => 'Entry Log',
            'route' => 'entrylogs.index'
        ],
    ]
];
