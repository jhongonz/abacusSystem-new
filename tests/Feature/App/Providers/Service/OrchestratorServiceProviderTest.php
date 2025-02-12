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
        $orchestratorMock = $this->createMock(GetCampusCollectionOrchestrator::class);

        $this->application->expects(self::once())
            ->method('tagged')
            ->with('orchestrators')
            ->willReturn([$orchestratorMock]);

        $orchestratorHandler = $this->createMock(OrchestratorHandler::class);
        $orchestratorHandler->expects(self::once())
            ->method('addOrchestrator')
            ->with($orchestratorMock)
            ->willReturnSelf();

        $this->application->expects(self::once())
            ->method('make')
            ->with(OrchestratorHandlerContract::class)
            ->willReturn($orchestratorHandler);

        $serviceProvider = new OrchestratorServiceProvider($this->application);
        $serviceProvider->boot();
    }

    public function testProvidesShouldReturnArrayCorrectly(): void
    {
        $serviceProvider = new OrchestratorServiceProvider($this->application);
        $provides = $serviceProvider->provides();

        $dataExpected = [
            OrchestratorHandlerContract::class,
        ];
        $this->assertIsArray($provides);
        $this->assertEquals($dataExpected, $provides);
    }
}
