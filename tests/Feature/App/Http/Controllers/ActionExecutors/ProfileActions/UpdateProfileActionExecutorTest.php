<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\ProfileActions;

use App\Http\Controllers\ActionExecutors\ProfileActions\UpdateProfileActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateProfileActionExecutor::class)]
class UpdateProfileActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private UpdateProfileActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->actionExecutor = new UpdateProfileActionExecutor($this->orchestratorHandler);
    }

    public function tearDown(): void
    {
        unset(
            $this->actionExecutor,
            $this->orchestratorHandler
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_invoke_should_return_profile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(3))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                [['id' => 1]],
                'name',
                'description',
            );

        $requestMock->expects(self::once())
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

        $profileMock = $this->createMock(Profile::class);
        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('update-profile', $requestMock)
            ->willReturn($profileMock);

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('update-profile-action', $result);
    }
}
