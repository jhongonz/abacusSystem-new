<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-20 14:19:53
 */

namespace Tests\Feature\App\Providers\Service;

use App\Providers\Service\InstitutionServiceProvider;
use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Infrastructure\Commands\InstitutionWarmup;
use Core\Institution\Infrastructure\Persistence\Repositories\ChainInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\EloquentInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Repositories\RedisInstitutionRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(InstitutionServiceProvider::class)]
class InstitutionServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(InstitutionServiceProvider::class);
    }

    /**
     * @throws Exception|BindingResolutionException
     */
    public function testBindsInstitutionRepositoryContractCorrectly(): void
    {
        $redisMock = $this->createMock(RedisInstitutionRepository::class);
        $this->app->singleton(RedisInstitutionRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $eloquentMock = $this->createMock(EloquentInstitutionRepository::class);
        $this->app->singleton(EloquentInstitutionRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $instance = $this->app->make(InstitutionRepositoryContract::class);

        $this->assertInstanceOf(ChainInstitutionRepository::class, $instance);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsInstitutionWarmupCorrectly(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->app->singleton(LoggerInterface::class, function () use ($loggerMock) {
            return $loggerMock;
        });

        $factoryMock = $this->createMock(InstitutionFactory::class);
        $this->app->singleton(InstitutionFactoryContract::class, function () use ($factoryMock) {
            return $factoryMock;
        });

        $eloquentMock = $this->createMock(EloquentInstitutionRepository::class);
        $this->app->singleton(EloquentInstitutionRepository::class, function () use ($eloquentMock) {
            return $eloquentMock;
        });

        $redisMock = $this->createMock(RedisInstitutionRepository::class);
        $this->app->singleton(RedisInstitutionRepository::class, function () use ($redisMock) {
            return $redisMock;
        });

        $instance = $this->app->make(InstitutionWarmup::class);

        $this->assertInstanceOf(InstitutionWarmup::class, $instance);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBootShouldAddRepositoryCorrectly(): void
    {
        $chainRepositoryMock = $this->createMock(ChainInstitutionRepository::class);
        $chainRepositoryMock->expects(self::exactly(2))
            ->method('addRepository')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(InstitutionRepositoryContract::class, function () use ($chainRepositoryMock) {
            return $chainRepositoryMock;
        });

        $serviceProvider = new InstitutionServiceProvider($this->app);
        $serviceProvider->boot();
    }

    public function testProvidesShouldReturnArrayCorrectly(): void
    {
        $serviceProvider = new InstitutionServiceProvider($this->app);
        $provides = $serviceProvider->provides();

        $dataExpected = [
            InstitutionFactoryContract::class,
            InstitutionDataTransformerContract::class,
            InstitutionManagementContract::class,
            InstitutionRepositoryContract::class,
        ];
        $this->assertIsArray($provides);
        $this->assertEquals($dataExpected, $provides);
    }
}
