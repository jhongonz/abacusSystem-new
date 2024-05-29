<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\EmployeeController;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
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
}
