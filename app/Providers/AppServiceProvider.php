<?php

namespace App\Providers;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Listeners\EmployeeWarmup;
use App\Listeners\ProfilesWarmup;
use App\Listeners\UserRefreshSession;
use App\Listeners\UserWarmup;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singletonIf(ImageManager::class, function (Application $app){
            return new ImageManager($app->make(Driver::class));
        });
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

        Event::listen(
            UserUpdateOrDeleteEvent::class,
            UserWarmup::class
        );
    }
}
