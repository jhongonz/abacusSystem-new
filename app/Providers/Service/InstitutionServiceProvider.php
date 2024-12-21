<?php

namespace App\Providers\Service;

use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Infrastructure\Commands\InstitutionWarmup;
use Core\Institution\Infrastructure\Management\InstitutionService;
use Core\Institution\Infrastructure\Persistence\Repositories\ChainInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\EloquentInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\RedisInstitutionRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class InstitutionServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        InstitutionFactoryContract::class => InstitutionFactory::class,
        InstitutionDataTransformerContract::class => InstitutionDataTransformer::class,
        InstitutionManagementContract::class => InstitutionService::class,
        InstitutionRepositoryContract::class => ChainInstitutionRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Commands
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
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $institutionRepository = $this->app->make(InstitutionRepositoryContract::class);
        $institutionRepository->addRepository($this->app->make(RedisInstitutionRepository::class));
        $institutionRepository->addRepository($this->app->make(EloquentInstitutionRepository::class));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            InstitutionFactoryContract::class,
            InstitutionDataTransformerContract::class,
            InstitutionManagementContract::class,
            InstitutionRepositoryContract::class,
        ];
    }
}
