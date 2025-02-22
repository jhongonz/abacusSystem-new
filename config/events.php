<?php

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\Institution\InstitutionUpdateOrDeletedEvent;
use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSessionEvent;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Listeners\CampusWarmupListener;
use App\Listeners\EmployeeWarmupListener;
use App\Listeners\InstitutionWarmupListener;
use App\Listeners\ProfilesWarmupListener;
use App\Listeners\UserRefreshSessionListener;
use App\Listeners\UserWarmupListener;

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-16 21:54:50
 */

return [
    /*
    |----------------------------------------------------------------------------------
    | Event Listeners handlers
    |----------------------------------------------------------------------------------
    |
    | Here you may define all the events listeners for the applications
    |
    */
    ModuleUpdatedOrDeletedEvent::class => ProfilesWarmupListener::class,
    ProfileUpdatedOrDeletedEvent::class => ProfilesWarmupListener::class,
    RefreshModulesSessionEvent::class => UserRefreshSessionListener::class,
    EmployeeUpdateOrDeletedEvent::class => EmployeeWarmupListener::class,
    UserUpdateOrDeleteEvent::class => UserWarmupListener::class,
    CampusUpdatedOrDeletedEvent::class => CampusWarmupListener::class,
    InstitutionUpdateOrDeletedEvent::class => InstitutionWarmupListener::class,
];
