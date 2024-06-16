<?php

namespace App\Providers\Service;

use Core\Institution\Application\DataTransformer\ContactCardInstitutionDataTransformer;
use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
use Core\Institution\Application\Factory\ContactCardInstitutionFactory;
use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Contracts\ContactCardInstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\ContactCardInstitutionFactoryContract;
use Core\Institution\Domain\Contracts\ContactCardInstitutionRepositoryContract;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Infrastructure\Commands\InstitutionWarmup;
use Core\Institution\Infrastructure\Management\InstitutionService;
use Core\Institution\Infrastructure\Persistence\Repositories\ChainContactCardInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\ChainInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\EloquentInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\RedisContactCardInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\RedisInstitutionRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class InstitutionServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     */
    public array $singletons = [
        InstitutionFactoryContract::class => InstitutionFactory::class,
        ContactCardInstitutionFactoryContract::class => ContactCardInstitutionFactory::class,
        InstitutionDataTransformerContract::class => InstitutionDataTransformer::class,
        ContactCardInstitutionDataTransformerContract::class => ContactCardInstitutionDataTransformer::class,
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

        $this->app->singletonIf(ContactCardInstitutionRepositoryContract::class, function (Application $app) {
            $chainRepository = new ChainContactCardInstitutionRepository;

            $chainRepository->addRepository(
                $app->make(RedisContactCardInstitutionRepository::class)
            );

            return $chainRepository;
        });

        //Commands
        $this->app->singletonIf(InstitutionWarmup::class, function (Application $app) {
            return new InstitutionWarmup(
                $app->make(LoggerInterface::class),
                $app->make(InstitutionFactoryContract::class),
                $app->make(EloquentInstitutionRepository::class),
                $app->make(RedisInstitutionRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
