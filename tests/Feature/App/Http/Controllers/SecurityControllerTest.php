<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\SecurityController;
use App\Http\Requests\User\LoginRequest;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserProfileId;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(SecurityController::class)]
class SecurityControllerTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private EmployeeManagementContract|MockObject $employeeManagement;
    private ProfileManagementContract|MockObject $profileManagement;
    private StatefulGuard|MockObject $statefulGuard;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private SecurityController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userFactory = $this->createMock(UserFactoryContract::class);
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->statefulGuard = $this->createMock(StatefulGuard::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->controller = new SecurityController(
            $this->userManagement,
            $this->employeeManagement,
            $this->profileManagement,
            $this->viewFactory,
            $this->logger,
            $this->statefulGuard
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->userManagement,
            $this->employeeManagement,
            $this->profileManagement,
            $this->statefulGuard,
            $this->viewFactory,
            $this->logger,
            $this->controller
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_index_should_return_response(): void
    {
        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn('<html lang="es"></html>');

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('home.login')
            ->willReturn($viewMock);

        $result = $this->controller->index();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertIsString($result->content());
        $this->assertSame('<html lang="es"></html>', $result->content());
    }

    /**
     * @throws Exception
     */
    public function test_authenticate_should_return_json_response_when_attempt_true_and_request_is_ajax(): void
    {
        $request = $this->createMock(LoginRequest::class);
        $request->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls('login', 'password');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $userMock = $this->createMock(User::class);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $userEmployeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($userEmployeeIdMock);

        $userProfileIdMock = $this->createMock(UserProfileId::class);
        $userProfileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($userProfileIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $userLoginMock = $this->createMock(UserLogin::class);
        $userLoginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($userLoginMock);

        $this->userManagement->expects(self::once())
            ->method('searchUserByLogin')
            ->with('login')
            ->willReturn($userMock);

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $profileMock = $this->createMock(Profile::class);

        $profileStateMock = $this->createMock(ProfileState::class);
        $profileStateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);

        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileStateMock);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $credentials = [
            'user_login' => 'login',
            'password' => 'password',
            'user_id' => 1
        ];
        $this->statefulGuard->expects(self::once())
            ->method('attempt')
            ->with($credentials)
            ->willReturn(true);

        $result = $this->controller->authenticate($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_authenticate_should_return_redirect_response_when_attempt_true_and_request_is_not_ajax(): void
    {
        $request = $this->createMock(LoginRequest::class);
        $request->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls('login', 'password');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $userEmployeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($userEmployeeIdMock);

        $userProfileIdMock = $this->createMock(UserProfileId::class);
        $userProfileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($userProfileIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $userLoginMock = $this->createMock(UserLogin::class);
        $userLoginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($userLoginMock);

        $this->userManagement->expects(self::once())
            ->method('searchUserByLogin')
            ->with('login')
            ->willReturn($userMock);

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $profileMock = $this->createMock(Profile::class);

        $profileStateMock = $this->createMock(ProfileState::class);
        $profileStateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);

        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileStateMock);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $credentials = [
            'user_login' => 'login',
            'password' => 'password',
            'user_id' => 1
        ];
        $this->statefulGuard->expects(self::once())
            ->method('attempt')
            ->with($credentials)
            ->willReturn(true);

        $result = $this->controller->authenticate($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(302, $result->getStatusCode());
        $this->assertSame('/home', $result->getTargetUrl());
    }

    /**
     * @throws Exception
     */
    public function test_authenticate_should_return_json_response_with_exception(): void
    {
        $request = $this->createMock(LoginRequest::class);
        $request->expects(self::once())
            ->method('input')
            ->withAnyParameters()
            ->willReturn('login');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $userMock = $this->createMock(User::class);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $userEmployeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($userEmployeeIdMock);

        $this->userManagement->expects(self::once())
            ->method('searchUserByLogin')
            ->with('login')
            ->willReturn($userMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willThrowException(new \Exception('Employee not found'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Employee not found');

        $result = $this->controller->authenticate($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(400, $result->getStatusCode());
        $this->assertSame(['message' => 'Bad credentials'], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_authenticate_should_return_redirect_response_with_exception(): void
    {
        $request = $this->createMock(LoginRequest::class);
        $request->expects(self::once())
            ->method('input')
            ->withAnyParameters()
            ->willReturn('login');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $userEmployeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($userEmployeeIdMock);

        $this->userManagement->expects(self::once())
            ->method('searchUserByLogin')
            ->with('login')
            ->willReturn($userMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willThrowException(new \Exception('Employee not found'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Employee not found');

        $result = $this->controller->authenticate($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(302, $result->getStatusCode());
        $this->assertSame('/login', $result->getTargetUrl());
    }

    /**
     * @throws Exception
     */
    public function test_authenticate_should_return_exception_when_profile_is_not_active(): void
    {
        $request = $this->createMock(LoginRequest::class);
        $request->expects(self::once())
            ->method('input')
            ->withAnyParameters()
            ->willReturn('login');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $userEmployeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($userEmployeeIdMock);

        $userProfileIdMock = $this->createMock(UserProfileId::class);
        $userProfileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($userProfileIdMock);

        $this->userManagement->expects(self::once())
            ->method('searchUserByLogin')
            ->with('login')
            ->willReturn($userMock);

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $profileMock = $this->createMock(Profile::class);

        $profileStateMock = $this->createMock(ProfileState::class);
        $profileStateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(true);

        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileStateMock);

        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $this->logger->expects(self::once())
            ->method('warning')
            ->with("User's profile with id: 1 is not active");

        $this->logger->expects(self::once())
            ->method('error')
            ->with('User is not authorized, contact with administrator');

        $result = $this->controller->authenticate($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(302, $result->getStatusCode());
        $this->assertSame('/login', $result->getTargetUrl());
    }

    /**
     * @throws Exception
     */
    public function test_home_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn('<html lang="es"></html>');

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->willReturn($viewMock);

        $result = $this->controller->home();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertArrayHasKey('html', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_home_should_return_string(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $requestMock);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn('<html lang="es"></html>');

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->willReturn($viewMock);

        $result = $this->controller->home();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame('<html lang="es"></html>', $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function test_logout_should_return_redirect(): void
    {
        $request = $this->createMock(Request::class);

        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects(self::once())
            ->method('flush');

        $sessionMock->expects(self::once())
            ->method('invalidate')
            ->willReturn(true);

        $sessionMock->expects(self::once())
            ->method('regenerateToken');

        $request->expects(self::exactly(3))
            ->method('session')
            ->willReturn($sessionMock);

        $this->statefulGuard->expects(self::once())
            ->method('logout');

        $result = $this->controller->logout($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(302, $result->getStatusCode());
        $this->assertSame('http://localhost/login', $result->getTargetUrl());
    }

    public function test_middleware_should_return_object(): void
    {
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(Middleware::class, $item);
        }
    }
}
