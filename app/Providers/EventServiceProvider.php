<?php

namespace App\Providers;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSessionEvent;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Listeners\CampusWarmupListener;
use App\Listeners\EmployeeWarmupListener;
use App\Listeners\ProfilesWarmupListener;
use App\Listeners\UserRefreshSessionListener;
use App\Listeners\UserWarmupListener;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<string> */
    private array $eventListeners = [
        ModuleUpdatedOrDeletedEvent::class => ProfilesWarmupListener::class,
        ProfileUpdatedOrDeletedEvent::class => ProfilesWarmupListener::class,
        RefreshModulesSessionEvent::class => UserRefreshSessionListener::class,
        EmployeeUpdateOrDeletedEvent::class => EmployeeWarmupListener::class,
        UserUpdateOrDeleteEvent::class => UserWarmupListener::class,
        CampusUpdatedOrDeletedEvent::class => CampusWarmupListener::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(Dispatcher $eventHandler): void
    {
        foreach ($this->eventListeners as $event => $listener) {
            $eventHandler->listen($event, $listener);
        }
    }
}
