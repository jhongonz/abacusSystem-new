<?php

namespace App\Providers\Service;

use Core\User\Application\DataTransformer\UserDataTransformer;
use Core\User\Application\Factory\UserFactory;
use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Infrastructure\Management\UserService;
use Core\User\Infrastructure\Persistence\Repositories\ChainUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\RedisUserRepository;
use Core\User\Infrastructure\Persistence\Translators\DomainToModelUserTranslator;
use Core\User\Infrastructure\Persistence\Translators\TranslatorContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Core\User\Domain\Contracts\UserFactoryContract;

class UserServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered
     *
     * @var array
     */
    public array $singletons = [
        UserFactoryContract::class => UserFactory::class,
        UserManagementContract::class => UserService::class,
        UserDataTransformerContract::class => UserDataTransformer::class,
        TranslatorContract::class => DomainToModelUserTranslator::class,
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryContract::class, function (Application $app){
            $chainRepository = new ChainUserRepository();
            
            $chainRepository->addRepository(
                $app->make(RedisUserRepository::class)
            )
            ->addRepository(
                $app->make(EloquentUserRepository::class)    
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