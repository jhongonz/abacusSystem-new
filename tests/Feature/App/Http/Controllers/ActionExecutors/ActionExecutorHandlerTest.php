<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Exceptions\ActionExecutorNotFoundException;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(ActionExecutorHandler::class)]
class ActionExecutorHandlerTest extends TestCase
{
    private ActionExecutorHandler $executorHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->executorHandler = new ActionExecutorHandler;
    }

    public function tearDown(): void
    {
        unset($this->executorHandler);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_addActionExecutor_should_add_and_return_self(): void
    {
        $actionExecutorMock = $this->createMock(ActionExecutor::class);
        $actionExecutorMock->expects(self::once())
            ->method('canExecute')
            ->willReturn('testing');

        $result = $this->executorHandler->addActionExecutor($actionExecutorMock);

        $this->assertInstanceOf(ActionExecutorHandler::class, $result);
        $this->assertSame($this->executorHandler, $result);
    }

    /**
     * @throws Exception
     * @throws ActionExecutorNotFoundException
     */
    public function test_invoke_should_return_mixed(): void
    {
        $requestMock = $this->createMock(Request::class);

        $actionExecutorMock = $this->createMock(ActionExecutor::class);
        $actionExecutorMock->expects(self::once())
            ->method('canExecute')
            ->willReturn('testing');

        $actionExecutorMock->expects(self::once())
            ->method('invoke')
            ->with($requestMock)
            ->willReturn('testing');

        $this->executorHandler->addActionExecutor($actionExecutorMock);
        $result = $this->executorHandler->invoke('testing', $requestMock);

        $this->assertSame('testing', $result);
    }

    /**
     * @throws Exception
     * @throws ActionExecutorNotFoundException
     */
    public function test_invoke_should_return_exception(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->expectException(ActionExecutorNotFoundException::class);

        $this->executorHandler->invoke('testing', $requestMock);
    }
}
