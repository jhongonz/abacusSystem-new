<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\EmployeeController;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\DataTables;

#[CoversClass(EmployeeController::class)]
class EmployeeControllerTest extends TestCase
{
    private EmployeeManagementContract|MockObject $employeeService;
    private EmployeeFactoryContract|MockObject $employeeFactory;
    private EmployeeDataTransformerContract|MockObject $employeeDataTransformer;
    private UserFactoryContract|MockObject $userFactory;
    private UserManagementContract|MockObject $userService;
    private ProfileManagementContract|MockObject $profileService;
    private DataTables|MockObject $dataTable;
    private ViewFactory|MockObject $viewFactory;
    private ImageManagerInterface|MockObject $imageManager;
    private LoggerInterface|MockObject $logger;
    private EmployeeController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeService = $this->createMock(EmployeeManagementContract::class);
        $this->employeeFactory = $this->createMock(EmployeeFactoryContract::class);
        $this->employeeDataTransformer = $this->createMock(EmployeeDataTransformerContract::class);
        $this->userFactory = $this->createMock(UserFactoryContract::class);
        $this->userService = $this->createMock(UserManagementContract::class);
        $this->profileService = $this->createMock(ProfileManagementContract::class);
        $this->dataTable = $this->createMock(DataTables::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->controller = new EmployeeController(
            $this->employeeService,
            $this->employeeFactory,
            $this->employeeDataTransformer,
            $this->userFactory,
            $this->userService,
            $this->profileService,
            $this->dataTable,
            $this->imageManager,
            $this->viewFactory,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->employeeService,
            $this->employeeFactory,
            $this->employeeDataTransformer,
            $this->userFactory,
            $this->userService,
            $this->profileService,
            $this->dataTable,
            $this->viewFactory,
            $this->imageManager,
            $this->logger,
            $this->controller
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
        $this->assertSame(['html' => $html], (array) $result->getData());
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
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function test_getEmployees_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $employeesMock = new Employees;
        $employeeMock = $this->createMock(Employee::class);
        $employeesMock->addItem($employeeMock);

        $this->employeeService->expects(self::once())
            ->method('searchEmployees')
            ->with([])
            ->willReturn($employeesMock);

        $this->employeeDataTransformer->expects(self::once())
            ->method('write')
            ->with($employeeMock)
            ->willReturnSelf();

        $dataExpected = ['hello' => 'word'];
        $this->employeeDataTransformer->expects(self::once())
            ->method('readToShare')
            ->willReturn($dataExpected);

        $arrayData = [];
        $arrayData[] = $dataExpected;
        $collectionMock = new Collection($arrayData);

        $callable = function (array $item) {
            return '<html lang="es"></html>';
        };
        $collectionDataTableMock = $this->createMock(CollectionDataTable::class);
        $collectionDataTableMock->expects(self::once())
            ->method('addColumn')
            ->with('tools', $callable)
            ->willReturnSelf();

        $collectionDataTableMock->expects(self::once())
            ->method('escapeColumns')
            ->with([])
            ->willReturnSelf();

        $jsonResponseMock = $this->createMock(JsonResponse::class);
        $collectionDataTableMock->expects(self::once())
            ->method('toJson')
            ->willReturn($jsonResponseMock);

        $this->dataTable->expects(self::once())
            ->method('collection')
            ->with($collectionMock)
            ->willReturn($collectionDataTableMock);

        $result = $this->controller->getEmployees($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($jsonResponseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_changeStateEmployee_should_activate_and_return_json_response(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('input')
            ->with('id')
            ->willReturn(10);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(10)
            ->willReturn($employeeIdMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::never())
            ->method('isInactivated');

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::exactly(4))
            ->method('state')
            ->willReturn($stateMock);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(10);
        $employeeMock->expects(self::exactly(2))
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $this->employeeService->expects(self::once())
            ->method('searchEmployeeById')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $dataUpdate['state'] = 2;
        $this->employeeService->expects(self::once())
            ->method('updateEmployee')
            ->with($employeeIdMock, $dataUpdate);

        $userIdMock = $this->createMock(UserId::class);
        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(10)
            ->willReturn($userIdMock);

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($userIdMock);

        $this->userService->expects(self::once())
            ->method('searchUserById')
            ->with($userIdMock)
            ->willReturn($userMock);

        $this->userService->expects(self::once())
            ->method('updateUser')
            ->with($userIdMock, $dataUpdate);

        $result = $this->controller->changeStateEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }
}
