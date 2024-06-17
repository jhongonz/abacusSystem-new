<?php

namespace App\Providers\Service;

use Core\Campus\Application\Factory\CampusFactory;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Illuminate\Support\ServiceProvider;

class CampusServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     */
    public array $singletons = [
        CampusFactoryContract::class => CampusFactory::class
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
    public function boot(): void
    {
    }
}
