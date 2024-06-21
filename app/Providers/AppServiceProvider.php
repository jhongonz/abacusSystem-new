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
            ProfilesWarmupListener::class
        );

        Event::listen(
            ProfileUpdatedOrDeletedEvent::class,
            ProfilesWarmupListener::class
        );

        Event::listen(
            RefreshModulesSessionEvent::class,
            UserRefreshSessionListener::class
        );

        Event::listen(
            EmployeeUpdateOrDeletedEvent::class,
            EmployeeWarmupListener::class
        );

        Event::listen(
            UserUpdateOrDeleteEvent::class,
            UserWarmupListener::class
        );

        Event::listen(
            CampusUpdatedOrDeletedEvent::class,
            CampusWarmupListener::class
        );
    }
}
