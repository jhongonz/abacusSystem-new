<?php

namespace App\Providers;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageManagerInterface;

class AppServiceProvider extends ServiceProvider
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

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
    }
}
