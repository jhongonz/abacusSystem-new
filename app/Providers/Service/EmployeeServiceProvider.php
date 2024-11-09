<?php

namespace App\Providers\Service;

use Core\Employee\Application\DataTransformer\EmployeeDataTransformer;
use Core\Employee\Application\Factory\EmployeeFactory;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Infrastructure\Commands\EmployeeWarmup;
use Core\Employee\Infrastructure\Management\EmployeeService;
use Core\Employee\Infrastructure\Persistence\Repositories\ChainEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Repositories\EloquentEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Repositories\RedisEmployeeRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class EmployeeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All the container bindings that should be registered
     * @var array<string, string> $singletons
     */
    public array $singletons = [
        EmployeeFactoryContract::class => EmployeeFactory::class,
        EmployeeManagementContract::class => EmployeeService::class,
        EmployeeDataTransformerContract::class => EmployeeDataTransformer::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(EmployeeRepositoryContract::class, function (Application $app) {
            $chainRepository = new ChainEmployeeRepository;

            $chainRepository->addRepository(
                $app->make(RedisEmployeeRepository::class)
            )
                ->addRepository(
                    $app->make(EloquentEmployeeRepository::class)
                );

            return $chainRepository;
        });

        //Commands
        $this->app->singletonIf(EmployeeWarmup::class, function (Application $app) {
            return new EmployeeWarmup(
                $app->make(LoggerInterface::class),
                $app->make(EmployeeFactoryContract::class),
                $app->make(EloquentEmployeeRepository::class),
                $app->make(RedisEmployeeRepository::class),
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            EmployeeFactoryContract::class,
            EmployeeManagementContract::class,
            EmployeeDataTransformerContract::class,
        ];
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
