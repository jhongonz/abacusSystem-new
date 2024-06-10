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
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageManagerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singletonIf(ImageManagerInterface::class, function (Application $app) {
            return new ImageManager($app->make(Driver::class));
        });

        $this->app->singletonIf(StatefulGuard::class, function (Application $app) {
            $authManager = $app->make(AuthManager::class);

            return $authManager->guard();
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
