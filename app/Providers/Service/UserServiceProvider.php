<?php

namespace App\Providers\Service;

use Core\User\Application\DataTransformer\UserDataTransformer;
use Core\User\Application\Factory\UserFactory;
use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Infrastructure\Commands\UserWarmup;
use Core\User\Infrastructure\Management\UserService;
use Core\User\Infrastructure\Persistence\Repositories\ChainUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\RedisUserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class UserServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        UserFactoryContract::class => UserFactory::class,
        UserManagementContract::class => UserService::class,
        UserDataTransformerContract::class => UserDataTransformer::class,
        UserRepositoryContract::class => ChainUserRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Commands
        $this->app->singletonIf(UserWarmup::class, function (Application $app) {
            return new UserWarmup(
                $app->make(LoggerInterface::class),
                $app->make(UserFactoryContract::class),
                $app->make(EloquentUserRepository::class),
                $app->make(RedisUserRepository::class),
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
        $userRepository = $this->app->make(UserRepositoryContract::class);
        $userRepository->addRepository($this->app->make(RedisUserRepository::class));
        $userRepository->addRepository($this->app->make(EloquentUserRepository::class));
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
            UserRepositoryContract::class,
        ];
    }
}
