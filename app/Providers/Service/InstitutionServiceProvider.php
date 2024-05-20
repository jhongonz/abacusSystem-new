<?php

namespace App\Providers\Service;

use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Infrastructure\Management\InstitutionService;
use Core\Institution\Infrastructure\Persistence\Repositories\ChainInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\EloquentInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\RedisInstitutionRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class InstitutionServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     */
    public array $singletons = [
        InstitutionFactoryContract::class => InstitutionFactory::class,
        InstitutionDataTransformerContract::class => InstitutionDataTransformer::class,
        InstitutionManagementContract::class => InstitutionService::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(InstitutionRepositoryContract::class, function (Application $app) {
            $chainRepository = new ChainInstitutionRepository;

            $chainRepository->addRepository(
                $app->make(RedisInstitutionRepository::class)
            );

            $chainRepository->addRepository(
                $app->make(EloquentInstitutionRepository::class)
            );

            return $chainRepository;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
