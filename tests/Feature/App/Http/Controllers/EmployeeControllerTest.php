<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\EmployeeController;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeIdentificationType;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profiles;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserPassword;
use Core\User\Domain\ValueObjects\UserPhoto;
use Core\User\Domain\ValueObjects\UserProfileId;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
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
    private Hasher|MockObject $hasher;
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
        $this->hasher = $this->createMock(Hasher::class);

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
            $this->logger,
            $this->hasher
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
            $this->hasher,
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

        $collectionDataTableMock = $this->createMock(CollectionDataTable::class);
        $collectionDataTableMock->expects(self::once())
            ->method('addColumn')
            ->with(
                'tools',
                $this->callback(function ($closure) {
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

                    $view = $closure(['id' => 1,'state' => 2]);

                    $this->assertIsString($view);
                    $this->assertSame('<html lang="es"></html>', $view);
                    return true;
                })
            )
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

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
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

    /**
     * @throws Exception
     */
    public function test_changeStateEmployee_should_inactivate_and_return_json_response(): void
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
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $stateMock->expects(self::never())
            ->method('activate');

        $stateMock->expects(self::once())
            ->method('inactive')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
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

        $dataUpdate['state'] = 1;
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

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeIdMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->employeeService->expects(self::once())
            ->method('searchEmployeeById')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $dataUpdate['state'] = 2;
        $this->employeeService->expects(self::once())
            ->method('updateEmployee')
            ->with($employeeIdMock, $dataUpdate)
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
    public function test_changeStateEmployee_should_return_exception_when_update_user(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('input')
            ->with('id')
            ->willReturn(1);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeIdMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

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
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(1)
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
            ->with($userIdMock, $dataUpdate)
            ->willThrowException(new \Exception('Can not update user'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('User with ID: 1 by employee with ID: 1 can not be updated');

        $result = $this->controller->changeStateEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getEmployee_with_null_should_return_json_response(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(null)
            ->willReturn($employeeIdMock);

        $profilesMock = $this->createMock(Profiles::class);
        $this->profileService->expects(self::once())
            ->method('searchProfiles')
            ->willReturn($profilesMock);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::exactly(6))
            ->method('with')
            ->withAnyParameters()
            ->willReturnSelf();

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.employee-form')
            ->willReturn($viewMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $result = $this->controller->getEmployee();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('html', $result->getData(true));
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getEmployee_with_data_should_return_json_response(): void
    {
        $id = 1;
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($id);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with($id)
            ->willReturn($employeeIdMock);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($id);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::exactly(2))
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $this->employeeService->expects(self::once())
            ->method('searchEmployeeById')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $userIdMock = $this->createMock(UserId::class);
        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with($id)
            ->willReturn($userIdMock);

        $userMock = $this->createMock(User::class);
        $this->userService->expects(self::once())
            ->method('searchUserById')
            ->with($userIdMock)
            ->willReturn($userMock);

        $profilesMock = $this->createMock(Profiles::class);
        $this->profileService->expects(self::once())
            ->method('searchProfiles')
            ->willReturn($profilesMock);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::exactly(6))
            ->method('with')
            ->withAnyParameters()
            ->willReturnSelf();

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.employee-form')
            ->willReturn($viewMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $result = $this->controller->getEmployee($id);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('html', $result->getData(true));
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getEmployee_with_null_should_return_string(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(null)
            ->willReturn($employeeIdMock);

        $profilesMock = $this->createMock(Profiles::class);
        $this->profileService->expects(self::once())
            ->method('searchProfiles')
            ->willReturn($profilesMock);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::exactly(6))
            ->method('with')
            ->withAnyParameters()
            ->willReturnSelf();

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('employee.employee-form')
            ->willReturn($viewMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $requestMock);

        $result = $this->controller->getEmployee();

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
        $request->expects(self::exactly(14))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                null,
                null,
                'identification',
                'name',
                'lastname',
                'typeDocument',
                'observations',
                'email',
                'address',
                'birthdate',
                'token',
                1,
                'login',
                'password',
            );

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(4))
            ->method('value')
            ->willReturnOnConsecutiveCalls(null, 1, 1, 1);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(null)
            ->willReturn($employeeIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturnOnConsecutiveCalls(null, 1);

        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(null)
            ->willReturn($userIdMock);

        $employeeIdentificationMock = $this->createMock(EmployeeIdentification::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeIdentification')
            ->with('identification')
            ->willReturn($employeeIdentificationMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeName')
            ->with('name')
            ->willReturn($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeLastname')
            ->with('lastname')
            ->willReturn($lastnameMock);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('id')
            ->willReturn($employeeIdMock);

        $typeDocumentMock = $this->createMock(EmployeeIdentificationType::class);
        $typeDocumentMock->expects(self::once())
            ->method('setValue')
            ->with('typeDocument')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($typeDocumentMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with('observations')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with('email')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with('address')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $birthdate = $this->createMock(EmployeeBirthdate::class);
        $birthdate->expects(self::once())
            ->method('setValue')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdate);

        $imageTmp = '/images/tmp/token.jpg';
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('/var/www/abacusSystem-new/public'.$imageTmp)
            ->willReturn($imageMock);

        $employeeImageMock = $this->createMock(EmployeeImage::class);
        $employeeImageMock->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($employeeImageMock);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployee')
            ->with(
                $employeeIdMock,
                $employeeIdentificationMock,
                $nameMock,
                $lastnameMock
            )
            ->willReturn($employeeMock);

        $this->employeeService->expects(self::once())
            ->method('createEmployee')
            ->with($employeeMock);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $this->userFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($userEmployeeIdMock);

        $profileMock = $this->createMock(UserProfileId::class);
        $this->userFactory->expects(self::once())
            ->method('buildProfileId')
            ->willReturn($profileMock);

        $loginMock = $this->createMock(UserLogin::class);
        $this->userFactory->expects(self::once())
            ->method('buildLogin')
            ->with('login')
            ->willReturn($loginMock);

        $this->hasher->expects(self::once())
            ->method('make')
            ->with('password')
            ->willReturn('hash');

        $passwordMock = $this->createMock(UserPassword::class);
        $this->userFactory->expects(self::once())
            ->method('buildPassword')
            ->with('hash')
            ->willReturn($passwordMock);

        $photoMock = $this->createMock(UserPhoto::class);
        $photoMock->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::once())
            ->method('photo')
            ->willReturn($photoMock);

        $this->userFactory->expects(self::once())
            ->method('buildUser')
            ->with(
                $userIdMock,
                $userEmployeeIdMock,
                $profileMock,
                $loginMock,
                $passwordMock
            )
            ->willReturn($userMock);

        $this->userService->expects(self::once())
            ->method('createUser')
            ->with($userMock);

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
        $request->expects(self::exactly(11))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                null,
                null,
                'identification',
                'name',
                'lastname',
                'typeDocument',
                'observations',
                'email',
                'address',
                'birthdate',
                'token',
            );

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(null);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(null)
            ->willReturn($employeeIdMock);

        $employeeIdentificationMock = $this->createMock(EmployeeIdentification::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeIdentification')
            ->with('identification')
            ->willReturn($employeeIdentificationMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeName')
            ->with('name')
            ->willReturn($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeLastname')
            ->with('lastname')
            ->willReturn($lastnameMock);

        $employeeMock = $this->createMock(Employee::class);

        $typeDocumentMock = $this->createMock(EmployeeIdentificationType::class);
        $typeDocumentMock->expects(self::once())
            ->method('setValue')
            ->with('typeDocument')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($typeDocumentMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with('observations')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with('email')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with('address')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $birthdate = $this->createMock(EmployeeBirthdate::class);
        $birthdate->expects(self::once())
            ->method('setValue')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdate);

        $imageTmp = '/var/www/abacusSystem-new/public/images/tmp/token.jpg';
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with($imageTmp)
            ->willReturn($imageMock);

        $employeeImageMock = $this->createMock(EmployeeImage::class);
        $employeeImageMock->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($employeeImageMock);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployee')
            ->with(
                $employeeIdMock,
                $employeeIdentificationMock,
                $nameMock,
                $lastnameMock
            )
            ->willReturn($employeeMock);

        $this->employeeService->expects(self::once())
            ->method('createEmployee')
            ->with($employeeMock)
            ->willThrowException(new \Exception('It has been error saving employee'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('It has been error saving employee');

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
            ->method('getRealPath')
            ->willReturn('localhost');

        $request->expects(self::once())
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
    public function test_deleteEmployee_should_return_json_response(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(10)
            ->willReturn($employeeIdMock);

        $employeeMock = $this->createMock(Employee::class);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

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

        $this->employeeService->expects(self::once())
            ->method('deleteEmployee')
            ->with($employeeIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(10)
            ->willReturn($userIdMock);

        $this->userService->expects(self::once())
            ->method('deleteUser')
            ->with($userIdMock);

        $result = $this->controller->deleteEmployee(10);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_deleteEmployee_should_return_json_response_with_exception(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(10)
            ->willReturn($employeeIdMock);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::never())
            ->method('image');

        $employeeMock->expects(self::never())
            ->method('userId');

        $this->employeeService->expects(self::once())
            ->method('searchEmployeeById')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $this->employeeService->expects(self::once())
            ->method('deleteEmployee')
            ->with($employeeIdMock)
            ->willThrowException(new \Exception('It can not delete employee'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('It can not delete employee');

        $this->userFactory->expects(self::never())
            ->method('buildId');

        $this->userService->expects(self::never())
            ->method('deleteUser');

        $result = $this->controller->deleteEmployee(10);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_deleteEmployee_should_return_json_response_with_exception_in_delete_user(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(10)
            ->willReturn($employeeIdMock);

        $employeeMock = $this->createMock(Employee::class);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

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

        $this->employeeService->expects(self::once())
            ->method('deleteEmployee')
            ->with($employeeIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(10)
            ->willReturn($userIdMock);

        $this->userService->expects(self::once())
            ->method('deleteUser')
            ->with($userIdMock)
            ->willThrowException(new \Exception('It can not delete user'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('It can not delete user');

        $result = $this->controller->deleteEmployee(10);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_storeEmployee_should_return_json_response_when_update_object(): void
    {
        $request = $this->createMock(StoreEmployeeRequest::class);
        $request->expects(self::exactly(15))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                10,
                10,
                null,
                'identifier',
                'typeDocument',
                'name',
                'lastname',
                'email',
                'phone',
                'address',
                'observations',
                1,
                'login',
                'token',
                'password'
            );

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(3))
            ->method('value')
            ->willReturn(10);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(10)
            ->willReturn($employeeIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(3))
            ->method('value')
            ->willReturn(10);

        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(10)
            ->willReturn($userIdMock);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $imageTmp = '/var/www/abacusSystem-new/public/images/tmp/token.jpg';
        $this->imageManager->expects(self::once())
            ->method('read')
            ->with($imageTmp)
            ->willReturn($imageMock);

        $this->employeeService->expects(self::once())
            ->method('updateEmployee')
            ->withAnyParameters();

        $this->hasher->expects(self::once())
            ->method('make')
            ->with('password')
            ->willReturn('hash');

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
        $request->expects(self::exactly(14))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                10,
                10,
                null,
                'identifier',
                'typeDocument',
                'name',
                'lastname',
                'email',
                'phone',
                'address',
                'observations',
                1,
                'login',
                'token',
            );

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(10);

        $this->employeeFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(10)
            ->willReturn($employeeIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::never())
            ->method('value');

        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->with(10)
            ->willReturn($userIdMock);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $imageTmp = '/var/www/abacusSystem-new/public/images/tmp/token.jpg';
        $this->imageManager->expects(self::once())
            ->method('read')
            ->with($imageTmp)
            ->willReturn($imageMock);

        $this->employeeService->expects(self::once())
            ->method('updateEmployee')
            ->withAnyParameters()
            ->willThrowException(new \Exception('It can not update employee'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('It can not update employee');

        $this->hasher->expects(self::never())
            ->method('make');

        $result = $this->controller->storeEmployee($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }
}
