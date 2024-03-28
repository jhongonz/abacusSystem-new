<?php

namespace App\Providers\Service;

use Core\User\Application\DataTransformer\UserDataTransformer;
use Core\User\Application\Factory\UserFactory;
use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Infrastructure\Commands\UserWarmup;
use Core\User\Infrastructure\Management\UserService;
use Core\User\Infrastructure\Persistence\Repositories\ChainUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\RedisUserRepository;
use Core\User\Infrastructure\Persistence\Translators\DomainToModelUserTranslator;
use Core\User\Infrastructure\Persistence\Translators\TranslatorContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Core\User\Domain\Contracts\UserFactoryContract;
use Psr\Log\LoggerInterface;

class UserServiceProvider extends ServiceProvider implements DeferrableProvider
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
        $this->app->singletonIf(UserRepositoryContract::class, function (Application $app){
            $chainRepository = new ChainUserRepository();

            $chainRepository->addRepository(
                $app->make(RedisUserRepository::class)
            )
            ->addRepository(
                $app->make(EloquentUserRepository::class)
            );

            return $chainRepository;
        });

        #Commands
        $this->app->singletonIf(UserWarmup::class, function (Application $app){
            return new UserWarmup(
                $app->make(LoggerInterface::class),
                $app->make(UserFactoryContract::class),
                $app->make(EloquentUserRepository::class),
                $app->make(RedisUserRepository::class),
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
            UserFactoryContract::class,
            UserManagementContract::class,
            UserDataTransformerContract::class,
            TranslatorContract::class
        ];
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
