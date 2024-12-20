<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-20 15:59:13
 */

namespace Tests\Feature\App\Providers\Service;

use App\Http\Orchestrators\Orchestrator\Campus\GetCampusCollectionOrchestrator;
use App\Http\Orchestrators\OrchestratorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Providers\Service\OrchestratorServiceProvider;
use Illuminate\Contracts\Config\Repository as Configuration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(OrchestratorServiceProvider::class)]
class OrchestratorServiceProviderTest extends TestCase
{
    private Application|MockObject $application;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->application = $this->createMock(Application::class);
    }

    protected function tearDown(): void
    {
        unset($this->application);
        parent::tearDown();
    }

    /**
     * @throws BindingResolutionException
     */
    public function testBindsOrchestratorHandlerContractCorrectly(): void
    {
        $instance = $this->app->make(OrchestratorHandlerContract::class);
        $this->assertInstanceOf(OrchestratorHandler::class, $instance);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootServiceProviderShouldInitializedCorrectly(): void
    {
        $orchestratorsMock = [
            GetCampusCollectionOrchestrator::class,
        ];
        $orchestratorMock = $this->createMock(GetCampusCollectionOrchestrator::class);

        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->expects(self::once())
            ->method('get')
            ->with('orchestrators')
            ->willReturn($orchestratorsMock);

        $orchestratorHandler = $this->createMock(OrchestratorHandler::class);

        $this->application->expects(self::exactly(3))
            ->method('make')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($configurationMock, $orchestratorHandler, $orchestratorMock);

        $serviceProvider = new OrchestratorServiceProvider($this->application);
        $serviceProvider->boot();
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBootServiceProviderShouldReturnException(): void
    {
        $orchestratorsMock = [
            'TestingClass',
        ];

        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->expects(self::once())
            ->method('get')
            ->with('orchestrators')
            ->willReturn($orchestratorsMock);

        $orchestratorHandler = $this->createMock(OrchestratorHandler::class);

        $this->application->expects(self::exactly(2))
            ->method('make')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($configurationMock, $orchestratorHandler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The orchestrator class TestingClass does not exists.');

        $serviceProvider = new OrchestratorServiceProvider($this->application);
        $serviceProvider->boot();
    }
}
