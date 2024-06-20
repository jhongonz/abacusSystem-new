<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\EmployeeController;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\User\Domain\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(EmployeeController::class)]
class EmployeeControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private ViewFactory|MockObject $viewFactory;
    private ImageManagerInterface|MockObject $imageManager;
    private LoggerInterface|MockObject $logger;
    private ActionExecutorHandler|MockObject $actionExecutorHandler;
    private EmployeeController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->actionExecutorHandler = $this->createMock(ActionExecutorHandler::class);

        $this->controller = new EmployeeController(
            $this->orchestrator,
            $this->actionExecutorHandler,
            $this->imageManager,
            $this->viewFactory,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->viewFactory,
            $this->imageManager,
            $this->logger,
            $this->controller,
            $this->actionExecutorHandler
        );

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_index_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('employees');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"employees"}')
            ->willReturnSelf();

        $html = '<html lang=""></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_index_should_return_string(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $requestMock);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('employees');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"employees"}')
            ->willReturnSelf();

        $html = '<html lang=""></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function test_getEmployees_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);

        $jsonResponseMock = $this->createMock(JsonResponse::class);
        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-employees', $requestMock)
            ->willReturn($jsonResponseMock);

        $result = $this->controller->getEmployees($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($jsonResponseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_changeStateEmployee_should_activate_and_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $employeeMock = $this->createMock(Employee::class);

        $employeeUserId = $this->createMock(EmployeeUserId::class);
        $employeeUserId->expects(self::once())
            ->method('value')
            ->willReturn(10);

        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($employeeUserId);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $userMock = $this->createMock(User::class);

        $this->orchestrator->expects(self::exactly(2))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($employeeMock, $userMock);

        $requestMock->expects(self::once())
            ->method('merge')
            ->with([
                'userId' => 10,
                'state' => 2
            ])
            ->willReturnSelf();

        $result = $this->controller->changeStateEmployee($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_changeStateEmployee_should_inactivate_and_return_json_response(): void
    {
        $request = $this->createMock(Request::class);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::once())
            ->method('value')
            ->willReturn(10);
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $userMock = $this->createMock(User::class);

        $request->expects(self::once())
            ->method('merge')
            ->with([
                'userId' => 10,
                'state' => 1,
            ])
            ->willReturnSelf();

        $this->orchestrator->expects(self::exactly(2))
            ->method('handler')
            ->willReturnOnConsecutiveCalls($employeeMock, $userMock);

        $result = $this->controller->changeStateEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_changeStateEmployee_should_return_exception_when_not_found_employee(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('input')
            ->with('id')
            ->willReturn(1);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('change-state-employee', $request)
            ->willThrowException(new \Exception('Can not update employee'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Employee can not be updated with id: 1');

        $result = $this->controller->changeStateEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getEmployee_with_null_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['employeeId' => null])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('detail-employee', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.employee-form', [])
            ->willReturn($viewMock);

        $this->app->instance(Request::class, $requestMock);
        $result = $this->controller->getEmployee($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('html', $result->getData(true));
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getEmployee_with_data_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $id = 1;
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['employeeId' => $id])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('detail-employee', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.employee-form', [])
            ->willReturn($viewMock);

        $this->app->instance(Request::class, $requestMock);
        $result = $this->controller->getEmployee($requestMock, $id);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('html', $result->getData(true));
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getEmployee_with_null_should_return_string(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['employeeId' => null])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('detail-employee', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.employee-form', [])
            ->willReturn($viewMock);

        $this->app->instance(Request::class, $requestMock);
        $result = $this->controller->getEmployee($requestMock);

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function test_storeEmployee_should_return_json_response_when_create_new_object(): void
    {
        $request = $this->createMock(StoreEmployeeRequest::class);
        $request->expects(self::once())
            ->method('filled')
            ->with('employeeId')
            ->willReturn(false);

        $employeeMock = $this->createMock(Employee::class);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($employeeIdMock);

        $employeeUserId = $this->createMock(EmployeeUserId::class);
        $employeeUserId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $employeeMock->expects(self::exactly(2))
            ->method('userId')
            ->willReturn($employeeUserId);

        $this->actionExecutorHandler->expects(self::once())
            ->method('invoke')
            ->with('create-employee-action', $request)
            ->willReturn($employeeMock);

        $result = $this->controller->storeEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('userId', $result->getData(true));
        $this->assertArrayHasKey('employeeId', $result->getData(true));
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_storeEmployee_should_return_json_response_with_exception_when_create_new_object(): void
    {
        $request = $this->createMock(StoreEmployeeRequest::class);
        $request->expects(self::once())
            ->method('filled')
            ->with('employeeId')
            ->willReturn(false);

        $this->actionExecutorHandler->expects(self::once())
            ->method('invoke')
            ->with('create-employee-action', $request)
            ->willThrowException(new \Exception('Can not create new employee'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not create new employee');

        $result = $this->controller->storeEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_setImageEmployee_should_return_json_response(): void
    {
        $request = $this->createMock(Request::class);

        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->expects(self::once())
            ->method('isValid')
            ->willReturn(true);

        $fileMock->expects(self::once())
            ->method('getRealPath')
            ->willReturn('localhost');

        $request->expects(self::exactly(2))
            ->method('file')
            ->with('file')
            ->willReturn($fileMock);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::once())
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('localhost')
            ->willReturn($imageMock);

        $result = $this->controller->setImageEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('token', $result->getData(true));
        $this->assertArrayHasKey('url', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_setImageEmployee_should_return_internal_error(): void
    {
        $request = $this->createMock(Request::class);

        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->expects(self::once())
            ->method('isValid')
            ->willReturn(false);

        $request->expects(self::once())
            ->method('file')
            ->with('file')
            ->willReturn($fileMock);

        $result = $this->controller->setImageEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_deleteEmployee_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::exactly(2))
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

        $employeeMock = $this->createMock(Employee::class);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $userIdMock = $this->createMock(EmployeeUserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($employeeMock, true, true);

        $result = $this->controller->deleteEmployee($requestMock, 10);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_deleteEmployee_should_return_json_response_with_exception(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['employeeId' => 10])
            ->willReturnSelf();

        $employeeMock = $this->createMock(Employee::class);
        $this->orchestrator->expects(self::exactly(2))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                $employeeMock,
                $this->throwException(new \Exception('Can not delete employee'))
            );

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not delete employee');

        $result = $this->controller->deleteEmployee($requestMock, 10);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_storeEmployee_should_return_json_response_when_update_object(): void
    {
        $request = $this->createMock(StoreEmployeeRequest::class);
        $request->expects(self::once())
            ->method('filled')
            ->with('employeeId')
            ->willReturn(true);

        $employeeMock = $this->createMock(Employee::class);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($employeeIdMock);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::exactly(2))
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $this->actionExecutorHandler->expects(self::once())
            ->method('invoke')
            ->with('update-employee-action')
            ->willReturn($employeeMock);

        $result = $this->controller->storeEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('userId', $result->getData(true));
        $this->assertArrayHasKey('employeeId', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_storeEmployee_should_return_json_response_with_exception_when_update_object(): void
    {
        $request = $this->createMock(StoreEmployeeRequest::class);
        $request->expects(self::once())
            ->method('filled')
            ->with('employeeId')
            ->willReturn(true);

        $this->actionExecutorHandler->expects(self::once())
            ->method('invoke')
            ->with('update-employee-action', $request)
            ->willThrowException(new \Exception('Can not update employee'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not update employee');

        $result = $this->controller->storeEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    public function test_middleware_should_return_object(): void
    {
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(Middleware::class, $item);
        }
    }
}
