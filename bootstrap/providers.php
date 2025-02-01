<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewFactoryServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\Service\CampusServiceProvider::class,
    App\Providers\Service\ControllerServiceProvider::class,
    App\Providers\Service\EmployeeServiceProvider::class,
    App\Providers\Service\InstitutionServiceProvider::class,
    App\Providers\Service\OrchestratorServiceProvider::class,
    App\Providers\Service\ProfileServiceProvider::class,
    App\Providers\Service\UserServiceProvider::class,
    Barryvdh\Debugbar\ServiceProvider::class,
    Yajra\DataTables\DataTablesServiceProvider::class,
];
