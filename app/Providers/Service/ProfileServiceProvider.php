<?php

namespace App\Providers\Service;

use Core\Profile\Application\DataTransformer\ModuleDataTransformer;
use Core\Profile\Application\DataTransformer\ProfileDataTransformer;
use Core\Profile\Application\Factory\ModuleFactory;
use Core\Profile\Application\Factory\ProfileFactory;
use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Infrastructure\Commands\ModuleWarmup;
use Core\Profile\Infrastructure\Commands\ProfileWarmup;
use Core\Profile\Infrastructure\Management\ModuleService;
use Core\Profile\Infrastructure\Management\ProfileService;
use Core\Profile\Infrastructure\Persistence\Repositories\ChainModuleRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\ChainProfileRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentModuleRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentProfileRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\RedisModuleRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\RedisProfileRepository;
use Core\Profile\Infrastructure\Persistence\Translators\DomainToModelModuleTranslator;
use Core\Profile\Infrastructure\Persistence\Translators\DomainToModelProfileTranslator;
use Core\Profile\Infrastructure\Persistence\Translators\TranslatorContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class ProfileServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     *
     * @var array
     */
    public array $singletons = [
        ProfileFactoryContract::class => ProfileFactory::class,
        ProfileDataTransformerContract::class => ProfileDataTransformer::class,
        ProfileManagementContract::class => ProfileService::class,

        /*Modules*/
        ModuleFactoryContract::class => ModuleFactory::class,
        ModuleDataTransformerContract::class => ModuleDataTransformer::class,
        ModuleManagementContract::class => ModuleService::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(ProfileRepositoryContract::class, function (Application $app){
            $chainRepository = new ChainProfileRepository();

            $chainRepository->addRepository(
                $app->make(RedisProfileRepository::class)
            )
            ->addRepository(
                $app->make(EloquentProfileRepository::class)
            );

            return $chainRepository;
        });

        $this->app->singletonIf(ModuleRepositoryContract::class, function (Application $app){
            $chainRepository = new ChainModuleRepository();

            $chainRepository->addRepository(
                $app->make(RedisModuleRepository::class)
            )
            ->addRepository(
                $app->make(EloquentModuleRepository::class)
            );

            return $chainRepository;
        });

        $this->app->when(EloquentProfileRepository::class)
            ->needs(TranslatorContract::class)
            ->give(DomainToModelProfileTranslator::class);

        $this->app->when(EloquentModuleRepository::class)
            ->needs(TranslatorContract::class)
            ->give(DomainToModelModuleTranslator::class);

        #Commands
        $this->app->singletonIf(ProfileWarmup::class, function(Application $app){
            return new ProfileWarmup(
                $app->make(LoggerInterface::class),
                $app->make(ProfileFactoryContract::class),
                $app->make(EloquentProfileRepository::class),
                $app->make(RedisProfileRepository::class),
            );
        });

        $this->app->singletonIf(ModuleWarmup::class, function(Application $app){
            return new ModuleWarmup(
                $app->make(LoggerInterface::class),
                $app->make(ModuleFactoryContract::class),
                $app->make(EloquentModuleRepository::class),
                $app->make(RedisModuleRepository::class),
            );
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
