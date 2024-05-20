<?php

namespace App\Providers\Service;

use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Illuminate\Support\ServiceProvider;

class InstitutionServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     */
    public array $singletons = [
        InstitutionFactoryContract::class => InstitutionFactory::class,
        InstitutionDataTransformerContract::class => InstitutionDataTransformer::class,
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
