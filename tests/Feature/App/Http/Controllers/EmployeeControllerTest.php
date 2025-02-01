<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Events\EventDispatcher;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\Controller;
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
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\DataTables;

#[CoversClass(EmployeeController::class)]
#[CoversClass(Controller::class)]
class EmployeeControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private ActionExecutorHandler|MockObject $actionExecutorHandler;
    private DataTables|MockObject $datatables;
    private EventDispatcher|MockObject $eventDispatcher;
    private ImageManagerInterface|MockObject $imageManager;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private Filesystem|MockObject $filesystem;
    private EmployeeController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->actionExecutorHandler = $this->createMock(ActionExecutorHandler::class);
        $this->datatables = $this->createMock(DataTables::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->filesystem = $this->createMock(Filesystem::class);

        $this->controller = new EmployeeController(
            $this->orchestrator,
            $this->actionExecutorHandler,
            $this->datatables,
            $this->eventDispatcher,
            $this->filesystem,
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
            $this->actionExecutorHandler,
            $this->datatables,
            $this->eventDispatcher,
            $this->filesystem
        );

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnJsonResponse(): void
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
    public function testIndexShouldReturnString(): void
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
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function testGetEmployeesShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-employees', $requestMock)
            ->willReturn([]);

        $collectionDataTableMock = $this->createMock(CollectionDataTable::class);
        $collectionDataTableMock->expects(self::once())
            ->method('addColumn')
            ->with('tools', $this->callback(function ($closure) {
                $viewMock = $this->createMock(View::class);
                $viewMock->expects(self::exactly(2))
                    ->method('with')
                    ->withAnyParameters()
                    ->willReturnSelf();

                $viewMock->expects(self::once())
                    ->method('render')
                    ->willReturn('<html lang="es"></html>');

                $this->viewFactory->expects(self::once())
                    ->method('make')
                    ->with('components.menu-options-datatable')
                    ->willReturn($viewMock);

                $view = $closure(['id' => 1, 'state' => 2]);

                $this->assertIsString($view);
                $this->assertSame('<html lang="es"></html>', $view);

                return true;
            }))
            ->willReturnSelf();

        $collectionDataTableMock->expects(self::once())
            ->method('escapeColumns')
            ->with([])
            ->willReturnSelf();

        $jsonResponseMock = $this->createMock(JsonResponse::class);
        $collectionDataTableMock->expects(self::once())
            ->method('toJson')
            ->willReturn($jsonResponseMock);

        $this->datatables->expects(self::once())
            ->method('collection')
            ->with([])
            ->willReturn($collectionDataTableMock);

        $result = $this->controller->getEmployees($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($jsonResponseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testChangeStateEmployeeShouldActivateAndReturnJsonResponse(): void
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
            ->willReturnOnConsecutiveCalls(
                ['employee' => $employeeMock],
                ['user' => $userMock]
            );

        $eventMock = new UserUpdateOrDeleteEvent(10);
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($eventMock);

        $requestMock->expects(self::once())
            ->method('merge')
            ->with([
                'userId' => 10,
                'state' => 2,
            ])
            ->willReturnSelf();

        $result = $this->controller->changeStateEmployee($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testChangeStateEmployeeShouldInactivateAndReturnJsonResponse(): void
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
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                ['employee' => $employeeMock],
                ['user' => $userMock]
            );

        $eventMock = new UserUpdateOrDeleteEvent(10);
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($eventMock);

        $result = $this->controller->changeStateEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testChangeStateEmployeeShouldReturnExceptionWhenNotFoundEmployee(): void
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
    public function testGetEmployeeWithNullShouldReturnJsonResponse(): void
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
    public function testGetEmployeeWithDataShouldReturnJsonResponse(): void
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
    public function testGetEmployeeWithNullShouldReturnString(): void
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
    public function testStoreEmployeeShouldReturnJsonResponseWhenCreateNewObject(): void
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

        $this->eventDispatcher->expects(self::exactly(2))
            ->method('dispatch')
            ->withAnyParameters();

        $result = $this->controller->storeEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('userId', $result->getData(true));
        $this->assertArrayHasKey('employeeId', $result->getData(true));
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreEmployeeShouldReturnJsonResponseWithExceptionWhenCreateNewObject(): void
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
    public function testSetImageEmployeeShouldReturnJsonResponse(): void
    {
        $request = $this->createMock(Request::class);

        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->expects(self::once())
            ->method('isValid')
            ->willReturn(true);

        $fileMock->expects(self::once())
            ->method('getRealPath')
            ->willReturn('localhost');

        $request->expects(self::once())
            ->method('file')
            ->with('file')
            ->willReturn($fileMock);

        Str::createRandomStringsUsing(function () {
            return '248ec6063c';
        });

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::once())
            ->method('save')
            ->with('/var/www/abacusSystem-new/public/images/tmp/248ec6063c.jpg')
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('localhost')
            ->willReturn($imageMock);

        $result = $this->controller->setImageEmployee($request);

        $dataResponseExpected = [
            'token' => '248ec6063c',
            'url' => 'http://localhost/images/tmp/248ec6063c.jpg',
        ];
        $dataResult = $result->getData(true);
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertSame($dataResponseExpected, $dataResult);
    }

    /**
     * @throws Exception
     */
    public function testSetImageEmployeeShouldReturnInternalError(): void
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
    public function testSetImageEmployeeShouldReturnInternalErrorWhenFileIsNotValid(): void
    {
        $request = $this->createMock(Request::class);

        $fileMock = $this->createMock(\stdClass::class);
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
    public function testDeleteEmployeeShouldReturnJsonResponse(): void
    {
        $employeeId = 10;
        $userId = 1;
        $requestMock = $this->createMock(Request::class);

        $callIndex = 0;
        $requestMock->expects(self::exactly(2))
            ->method('merge')
            ->willReturnCallback(function ($input) use (&$callIndex, &$userId, &$employeeId) {
                $this->assertIsArray($input);
                if (0 === $callIndex) {
                    $this->assertEquals(['employeeId' => $employeeId], $input);
                } elseif (1 === $callIndex) {
                    $this->assertSame(['userId' => $userId], $input);
                }

                ++$callIndex;
            });

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
            ->willReturn($userId);
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $this->filesystem->expects(self::once())
            ->method('delete')
            ->with([
                '/var/www/abacusSystem-new/public/images/full/image.jpg',
                '/var/www/abacusSystem-new/public/images/small/image.jpg',
            ])
            ->willReturn(true);

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(['employee' => $employeeMock], [], []);

        $result = $this->controller->deleteEmployee($requestMock, $employeeId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteEmployeeShouldReturnJsonResponseWhenImageIsNull(): void
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
            ->willReturn(null);
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
            ->willReturnOnConsecutiveCalls(['employee' => $employeeMock], [], []);

        $result = $this->controller->deleteEmployee($requestMock, 10);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteEmployeeShouldReturnJsonResponseWithException(): void
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
                ['employee' => $employeeMock],
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
    public function testStoreEmployeeShouldReturnJsonResponseWhenUpdateObject(): void
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
    public function testStoreEmployeeShouldReturnJsonResponseWithExceptionWhenUpdateObject(): void
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

    public function testMiddlewareShouldReturnObject(): void
    {
        $dataExpected = [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getEmployees', 'setImageEmployee', 'deleteEmployee', 'changeStateEmployee', 'storeEmployee',
            ]),
        ];
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Middleware::class, $result);
        $this->assertEquals($dataExpected, $result);
    }
}
