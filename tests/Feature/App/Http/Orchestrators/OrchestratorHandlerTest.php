<?php

namespace Tests\Feature\App\Http\Orchestrators;

use App\Http\Orchestrators\Exceptions\DuplicateOrchestratorException;
use App\Http\Orchestrators\Orchestrator\Orchestrator;
use App\Http\Orchestrators\OrchestratorHandler;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(OrchestratorHandler::class)]
class OrchestratorHandlerTest extends TestCase
{
    private OrchestratorHandler $orchestratorHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = new OrchestratorHandler;
    }

    public function tearDown(): void
    {
        unset($this->orchestratorHandler);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws DuplicateOrchestratorException
     */
    public function test_handler_should_return_mixed(): void
    {
        $requestMock = $this->createMock(Request::class);

        $orchestratorMock = $this->createMock(Orchestrator::class);
        $orchestratorMock->expects(self::exactly(2))
            ->method('canOrchestrate')
            ->willReturn('testing');
        $orchestratorMock->expects(self::once())
            ->method('make')
            ->with($requestMock)
            ->willReturn('testing');
        $this->orchestratorHandler->addOrchestrator($orchestratorMock);

        $result = $this->orchestratorHandler->handler('testing', $requestMock);

        $this->assertSame('testing', $result);
    }

    /**
     * @throws Exception
     * @throws DuplicateOrchestratorException
     */
    public function test_addOrchestrator_should_return_exception(): void
    {
        $orchestratorMock1 = $this->createMock(Orchestrator::class);
        $orchestratorMock1->expects(self::exactly(2))
            ->method('canOrchestrate')
            ->willReturn('testing');
        $this->orchestratorHandler->addOrchestrator($orchestratorMock1);

        $orchestratorMock2 = $this->createMock(Orchestrator::class);
        $orchestratorMock2->expects(self::exactly(2))
            ->method('canOrchestrate')
            ->willReturn('testing');

        $this->expectException(DuplicateOrchestratorException::class);
        $this->expectExceptionMessage('testing orchestrate duplicate');

        $this->orchestratorHandler->addOrchestrator($orchestratorMock2);
    }
}
