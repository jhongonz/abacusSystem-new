<?php

namespace App\Providers\Service;

use Core\Employee\Application\DataTransformer\EmployeeDataTransformer;
use Core\Employee\Application\Factory\EmployeeFactory;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Infrastructure\Management\EmployeeService;
use Core\Employee\Infrastructure\Persistence\Repositories\ChainEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Repositories\EloquentEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Repositories\RedisEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Translators\DomainToModelEmployeeTranslator;
use Core\Employee\Infrastructure\Persistence\Translators\TranslatorContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     *
     * @var array
     */
    public array $singletons = [
        EmployeeFactoryContract::class => EmployeeFactory::class,
        EmployeeManagementContract::class => EmployeeService::class,
        EmployeeDataTransformerContract::class => EmployeeDataTransformer::class,
        TranslatorContract::class => DomainToModelEmployeeTranslator::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(EmployeeRepositoryContract::class, function (Application $app){
            $chainRepository = new ChainEmployeeRepository();

            $chainRepository->addRepository(
                $app->make(RedisEmployeeRepository::class)
            )
            ->addRepository(
                $app->make(EloquentEmployeeRepository::class)
            );

            return $chainRepository;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
