<?php

return [
    'options' => [
        [
            'id' => null,
            'key' => 'managers',
            'name' => 'Gestión Administrativa',
            'icon' => 'fas fa-tools',
            'route' => '',
        ],
        [
            'id' => null,
            'key' => 'settings',
            'name' => 'Configuraciones',
            'icon' => 'fas fa-tools',
            'route' => '',
        ],
        /*[
            'name' => 'Gestión de Empleados',
            'icon' => 'fas fa-tools',
            'route' => 'localhost',
        ],*/
    ],

    /*
     * Privileges
     */
    'permission' => [
        'read' => [
            'name' => 'Lectura',
        ],
        'read-update' => [
            'name' => 'Lectura y modificación',
        ],
        'read-update-create' => [
            'name' => 'Lectura, modificación y creación',
        ],
    ],
];
