<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\InstitutionActions;

use App\Http\Controllers\ActionExecutors\InstitutionActions\CreateInstitutionActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateInstitutionActionExecutor::class)]
class CreateInstitutionActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private CreateInstitutionActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->actionExecutor = new CreateInstitutionActionExecutor($this->orchestratorHandler);
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestratorHandler,
            $this->actionExecutor
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_invoke_should_return_institution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $institutionMock = $this->createMock(Institution::class);

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('create-institution', $requestMock)
            ->willReturn($institutionMock);

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Institution::class, $institutionMock);
        $this->assertSame($institutionMock, $result);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('create-institution-action', $result);
    }
}
