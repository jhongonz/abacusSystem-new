<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-20 21:32:27
 */

namespace Tests\Feature\App\Providers\Service;

use App\Providers\Service\ProfileServiceProvider;
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
use Core\Profile\Infrastructure\Persistence\Repositories\ChainModuleRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\ChainProfileRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentModuleRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentProfileRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\RedisModuleRepository;
use Core\Profile\Infrastructure\Persistence\Repositories\RedisProfileRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(ProfileServiceProvider::class)]
class ProfileServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(ProfileServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsProfileRepositoryContractCorrectly(): void
    {
        $redisMock = $this->createMock(RedisProfileRepository::class);
        $this->app->singleton(RedisProfileRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $eloquentMock = $this->createMock(EloquentProfileRepository::class);
        $this->app->singleton(EloquentProfileRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $instance = $this->app->make(ProfileRepositoryContract::class);
        $this->assertInstanceOf(ChainProfileRepository::class, $instance);
    }

    public function testBindsModuleRepositoryContractCorrectly(): void
    {
        $redisMock = $this->createMock(RedisModuleRepository::class);
        $this->app->singleton(RedisModuleRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $eloquentMock = $this->createMock(EloquentModuleRepository::class);
        $this->app->singleton(EloquentModuleRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $instance = $this->app->make(ModuleRepositoryContract::class);
        $this->assertInstanceOf(ChainModuleRepository::class, $instance);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBindsProfileWarmupCorrectly(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->app->singleton(LoggerInterface::class, function () use ($loggerMock) {
            return $loggerMock;
        });

        $factoryMock = $this->createMock(ProfileFactory::class);
        $this->app->singleton(ProfileFactoryContract::class, function () use ($factoryMock) {
            return $factoryMock;
        });

        $eloquentMock = $this->createMock(EloquentProfileRepository::class);
        $this->app->singleton(EloquentProfileRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $redisMock = $this->createMock(RedisProfileRepository::class);
        $this->app->singleton(RedisProfileRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $instance = $this->app->make(ProfileWarmup::class);
        $this->assertInstanceOf(ProfileWarmup::class, $instance);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBindsModuleWarmupCorrectly(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->app->singleton(LoggerInterface::class, function () use ($loggerMock) {
            return $loggerMock;
        });

        $factoryMock = $this->createMock(ModuleFactory::class);
        $this->app->singleton(ModuleFactoryContract::class, function () use ($factoryMock) {
            return $factoryMock;
        });

        $eloquentMock = $this->createMock(EloquentModuleRepository::class);
        $this->app->singleton(EloquentModuleRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $redisMock = $this->createMock(RedisModuleRepository::class);
        $this->app->singleton(RedisModuleRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $instance = $this->app->make(ModuleWarmup::class);
        $this->assertInstanceOf(ModuleWarmup::class, $instance);
    }

    public function testProvidesShouldReturnArrayCorrectly(): void
    {
        $serviceProvider = new ProfileServiceProvider($this->app);
        $provides = $serviceProvider->provides();

        $dataExpected = [
            ProfileFactoryContract::class,
            ProfileDataTransformerContract::class,
            ProfileManagementContract::class,
            ModuleFactoryContract::class,
            ModuleDataTransformerContract::class,
            ModuleManagementContract::class,
        ];
        $this->assertIsArray($provides);
        $this->assertEquals($dataExpected, $provides);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootShouldAddRepositoryProfileCorrectly(): void
    {
        $chainRepositoryMock = $this->createMock(ChainProfileRepository::class);
        $chainRepositoryMock->expects(self::exactly(2))
            ->method('addRepository')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(ProfileRepositoryContract::class, function () use ($chainRepositoryMock) {
            return $chainRepositoryMock;
        });

        $serviceProvider = new ProfileServiceProvider($this->app);
        $serviceProvider->boot();
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootShouldAddRepositoryModuleCorrectly(): void
    {
        $chainRepositoryMock = $this->createMock(ChainModuleRepository::class);
        $chainRepositoryMock->expects(self::exactly(2))
            ->method('addRepository')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(ModuleRepositoryContract::class, function () use ($chainRepositoryMock) {
            return $chainRepositoryMock;
        });

        $serviceProvider = new ProfileServiceProvider($this->app);
        $serviceProvider->boot();
    }
}
