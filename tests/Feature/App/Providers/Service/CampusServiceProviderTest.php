<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-19 22:33:46
 */

namespace Tests\Feature\App\Providers\Service;

use App\Providers\Service\CampusServiceProvider;
use Core\Campus\Application\Factory\CampusFactory;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Infrastructure\Commands\CampusWarmup;
use Core\Campus\Infrastructure\Persistence\Repositories\ChainCampusRepository;
use Core\Campus\Infrastructure\Persistence\Repositories\EloquentCampusRepository;
use Core\Campus\Infrastructure\Persistence\Repositories\RedisCampusRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(CampusServiceProvider::class)]
class CampusServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(CampusServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsCampusRepositoryContractCorrectly(): void
    {
        $redisRepositoryMock = $this->createMock(RedisCampusRepository::class);
        $this->app->singleton(RedisCampusRepository::class, function () use ($redisRepositoryMock) {
            return $redisRepositoryMock;
        });

        $eloquentRepositoryMock = $this->createMock(EloquentCampusRepository::class);
        $this->app->singleton(EloquentCampusRepository::class, function () use ($eloquentRepositoryMock) {
            return $eloquentRepositoryMock;
        });

        $instance = $this->app->make(CampusRepositoryContract::class);
        $this->assertInstanceOf(ChainCampusRepository::class, $instance);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsCampusWarmupCorrectly(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->app->singleton(LoggerInterface::class, function () use ($logger) {
            return $logger;
        });

        $factory = $this->createMock(CampusFactory::class);
        $this->app->singleton(CampusFactoryContract::class, function () use ($factory) {
            return $factory;
        });

        $eloquentRepositoryMock = $this->createMock(EloquentCampusRepository::class);
        $this->app->singleton(EloquentCampusRepository::class, function () use ($eloquentRepositoryMock) {
            return $eloquentRepositoryMock;
        });

        $redisCampusRepository = $this->createMock(RedisCampusRepository::class);
        $this->app->singleton(RedisCampusRepository::class, function () use ($redisCampusRepository) {
            return $redisCampusRepository;
        });

        $instance = $this->app->make(CampusWarmup::class);

        $this->assertInstanceOf(CampusWarmup::class, $instance);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootShouldAddRepositoryCorrectly(): void
    {
        $chainRepositoryMock = $this->createMock(ChainCampusRepository::class);
        $chainRepositoryMock->expects(self::exactly(2))
            ->method('addRepository')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(CampusRepositoryContract::class, function () use ($chainRepositoryMock) {
            return $chainRepositoryMock;
        });

        $serviceProvider = new CampusServiceProvider($this->app);
        $serviceProvider->boot();
    }
}
