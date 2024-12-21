<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-20 22:07:15
 */

namespace Tests\Feature\App\Providers\Service;

use App\Providers\Service\UserServiceProvider;
use Core\User\Application\Factory\UserFactory;
use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Infrastructure\Commands\UserWarmup;
use Core\User\Infrastructure\Persistence\Repositories\ChainUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Core\User\Infrastructure\Persistence\Repositories\RedisUserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(UserServiceProvider::class)]
class UserServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(UserServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsUserRepositoryContractCorrectly(): void
    {
        $redisMock = $this->createMock(RedisUserRepository::class);
        $this->app->singleton(RedisUserRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $eloquentMock = $this->createMock(EloquentUserRepository::class);
        $this->app->singleton(EloquentUserRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $instance = $this->app->make(UserRepositoryContract::class);
        $this->assertInstanceOf(ChainUserRepository::class, $instance);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBindsUserWarmupCorrectly(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->app->singleton(LoggerInterface::class, function () use ($loggerMock) {
            return $loggerMock;
        });

        $factoryMock = $this->createMock(UserFactory::class);
        $this->app->singleton(UserFactoryContract::class, function () use ($factoryMock) {
            return $factoryMock;
        });

        $eloquentMock = $this->createMock(EloquentUserRepository::class);
        $this->app->singleton(EloquentUserRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $redisMock = $this->createMock(RedisUserRepository::class);
        $this->app->singleton(RedisUserRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $instance = $this->app->make(UserWarmup::class);
        $this->assertInstanceOf(UserWarmup::class, $instance);
    }

    public function testProvidesShouldReturnArrayCorrectly(): void
    {
        $serviceProvider = new UserServiceProvider($this->app);
        $provides = $serviceProvider->provides();

        $dataExpected = [
            UserFactoryContract::class,
            UserManagementContract::class,
            UserDataTransformerContract::class,
            UserRepositoryContract::class,
        ];
        $this->assertISArray($provides);
        $this->assertEquals($dataExpected, $provides);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootShouldAddRepositoryCorrectly(): void
    {
        $chainRepositoryMock = $this->createMock(ChainUserRepository::class);
        $chainRepositoryMock->expects(self::exactly(2))
            ->method('addRepository')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(UserRepositoryContract::class, function () use ($chainRepositoryMock) {
            return $chainRepositoryMock;
        });

        $serviceProvider = new UserServiceProvider($this->app);
        $serviceProvider->boot();
    }
}
