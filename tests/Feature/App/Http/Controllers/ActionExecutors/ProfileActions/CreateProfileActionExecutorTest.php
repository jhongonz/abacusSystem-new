<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\ProfileActions;

use App\Http\Controllers\ActionExecutors\ProfileActions\CreateProfileActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateProfileActionExecutor::class)]
class CreateProfileActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private CreateProfileActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->actionExecutor = new CreateProfileActionExecutor($this->orchestratorHandler);
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
    public function test_invoke_should_return_profile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('modules')
            ->willReturn([
                ['id' => 1]
            ]);

        $requestMock->expects(self::once())
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

        $profileMock = $this->createMock(Profile::class);
        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('create-profile', $requestMock)
            ->willReturn($profileMock);

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('create-profile-action', $result);
    }
}
