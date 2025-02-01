<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-19 23:25:35
 */

namespace Tests\Feature\App\Providers\Service;

use App\Providers\Service\EmployeeServiceProvider;
use Core\Employee\Application\Factory\EmployeeFactory;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Infrastructure\Commands\EmployeeWarmup;
use Core\Employee\Infrastructure\Persistence\Repositories\ChainEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Repositories\EloquentEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Repositories\RedisEmployeeRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(EmployeeServiceProvider::class)]
class EmployeeServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(EmployeeServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsEmployeeRepositoryContractCorrectly(): void
    {
        $redisMock = $this->createMock(RedisEmployeeRepository::class);
        $this->app->singleton(RedisEmployeeRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $eloquentMock = $this->createMock(EloquentEmployeeRepository::class);
        $this->app->singleton(EloquentEmployeeRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $instance = $this->app->make(EmployeeRepositoryContract::class);

        $this->assertInstanceOf(ChainEmployeeRepository::class, $instance);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsEmployeeWarmupCorrectly(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->app->singleton(LoggerInterface::class, function () use ($loggerMock) {
            return $loggerMock;
        });

        $factory = $this->createMock(EmployeeFactory::class);
        $this->app->singleton(EmployeeFactory::class, function () use ($factory) {
            return $factory;
        });

        $eloquentMock = $this->createMock(EloquentEmployeeRepository::class);
        $this->app->singleton(EloquentEmployeeRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $redisMock = $this->createMock(RedisEmployeeRepository::class);
        $this->app->singleton(RedisEmployeeRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $instance = $this->app->make(EmployeeWarmup::class);

        $this->assertInstanceOf(EmployeeWarmup::class, $instance);
    }

    public function testProviderShouldReturnArray(): void
    {
        $instance = new EmployeeServiceProvider($this->app);
        $result = $instance->provides();

        $dataExpected = [
            EmployeeFactoryContract::class,
            EmployeeManagementContract::class,
            EmployeeDataTransformerContract::class,
            EmployeeRepositoryContract::class,
        ];

        $this->assertIsArray($result);
        $this->assertEquals($dataExpected, $result);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootShouldAddRepositoryCorrectly(): void
    {
        $chainRepositoryMock = $this->createMock(ChainEmployeeRepository::class);
        $chainRepositoryMock->expects(self::once())
            ->method('addRepository')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(EmployeeRepositoryContract::class, function () use ($chainRepositoryMock) {
            return $chainRepositoryMock;
        });

        $serviceProvider = new EmployeeServiceProvider($this->app);
        $serviceProvider->boot();
    }
}
