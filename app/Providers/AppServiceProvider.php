<?php

namespace App\Providers;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Listeners\EmployeeWarmup;
use App\Listeners\ProfilesWarmup;
use App\Listeners\UserRefreshSession;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            ModuleUpdatedOrDeletedEvent::class,
            ProfilesWarmup::class
        );

        Event::listen(
            ProfileUpdatedOrDeletedEvent::class,
            ProfilesWarmup::class
        );

        Event::listen(
            RefreshModulesSession::class,
            UserRefreshSession::class
        );

        Event::listen(
            EmployeeUpdateOrDeletedEvent::class,
            EmployeeWarmup::class
        );
    }
}
