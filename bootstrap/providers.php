<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
    
    /*
     * Package Service Providers...
     */
    App\Providers\Service\UserServiceProvider::class,
    App\Providers\Service\EmployeeServiceProvider::class,
    App\Providers\Service\ProfileServiceProvider::class,
    Yajra\DataTables\DataTablesServiceProvider::class,
];
