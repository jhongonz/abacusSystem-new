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
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class CampusServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     * @var array<string, string> $singletons
     */
    public array $singletons = [
        CampusFactoryContract::class => CampusFactory::class,
        CampusDataTransformerContract::class => CampusDataTransformer::class,
        CampusManagementContract::class => CampusService::class
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(CampusRepositoryContract::class, function (Application $app) {
            $chainRepository = new ChainCampusRepository;

            $chainRepository->addRepository(
                $app->make(RedisCampusRepository::class)
            );

            $chainRepository->addRepository(
                $app->make(EloquentCampusRepository::class)
            );

            return $chainRepository;
        });

        //Commands
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
     */
    public function boot(): void
    {
    }
}
