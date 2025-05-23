<?php

namespace App\Providers\Service;

use Core\Campus\Application\DataTransformer\CampusDataTransformer;
use Core\Campus\Application\Factory\CampusFactory;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Infrastructure\Commands\CampusWarmup;
use Core\Campus\Infrastructure\Management\CampusService;
use Core\Campus\Infrastructure\Persistence\Repositories\ChainCampusRepository;
use Core\Campus\Infrastructure\Persistence\Repositories\EloquentCampusRepository;
use Core\Campus\Infrastructure\Persistence\Repositories\RedisCampusRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class CampusServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        CampusFactoryContract::class => CampusFactory::class,
        CampusDataTransformerContract::class => CampusDataTransformer::class,
        CampusManagementContract::class => CampusService::class,
        CampusRepositoryContract::class => ChainCampusRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Commands
        $this->app->singletonIf(CampusWarmup::class, function (Application $app) {
            return new CampusWarmup(
                $app->make(LoggerInterface::class),
                $app->make(CampusFactoryContract::class),
                $app->make(EloquentCampusRepository::class),
                $app->make(RedisCampusRepository::class)
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
        $campusRepository = $this->app->make(CampusRepositoryContract::class);
        $campusRepository->addRepository(
            $this->app->make(RedisCampusRepository::class),
            $this->app->make(EloquentCampusRepository::class)
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            CampusRepositoryContract::class,
            CampusDataTransformerContract::class,
            CampusManagementContract::class,
            CampusRepositoryContract::class,
        ];
    }
}
