<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\SecurityController;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\User\LoginRequest;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileState;
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
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private StatefulGuard|MockObject $statefulGuard;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private Session|MockObject $session;
    private SecurityController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->statefulGuard = $this->createMock(StatefulGuard::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->session = $this->createMock(Session::class);

        $this->controller = new SecurityController(
            $this->orchestrator,
            $this->viewFactory,
            $this->logger,
            $this->statefulGuard,
            $this->session
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->statefulGuard,
            $this->viewFactory,
            $this->logger,
            $this->controller,
            $this->session,
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
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('input')
            ->with('password')
            ->willReturn('password');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $userMock = $this->createMock(User::class);

        $userLoginMock = $this->createMock(UserLogin::class);
        $userLoginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($userLoginMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $employeeIdMock = $this->createMock(UserEmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeIdMock);

        $profileIdMock = $this->createMock(UserProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(2);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileIdMock);

        $employeeMock = $this->createMock(Employee::class);
        $profileMock = $this->createMock(Profile::class);

        $profileState = $this->createMock(ProfileState::class);
        $profileState->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileState);

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($userMock, $employeeMock, $profileMock);

        $this->statefulGuard->expects(self::once())
            ->method('attempt')
            ->with([
                'user_login' => 'login',
                'password' => 'password',
                'user_id' => 1
            ])
            ->willReturn(true);

        $this->session->expects(self::once())
            ->method('regenerate')
            ->willReturn(true);

        $this->session->expects(self::once())
            ->method('put')
            ->with([
                'user' => $userMock,
                'profile' => $profileMock,
                'employee' => $employeeMock
            ]);

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
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('input')
            ->with('password')
            ->willReturn('password');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);

        $userLoginMock = $this->createMock(UserLogin::class);
        $userLoginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($userLoginMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $employeeIdMock = $this->createMock(UserEmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeIdMock);

        $profileIdMock = $this->createMock(UserProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(2);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileIdMock);

        $employeeMock = $this->createMock(Employee::class);
        $profileMock = $this->createMock(Profile::class);

        $profileState = $this->createMock(ProfileState::class);
        $profileState->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileState);

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($userMock, $employeeMock, $profileMock);

        $this->statefulGuard->expects(self::once())
            ->method('attempt')
            ->with([
                'user_login' => 'login',
                'password' => 'password',
                'user_id' => 1
            ])
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

        $request->expects(self::exactly(2))
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('input')
            ->with('password')
            ->willReturn('password');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $userMock = $this->createMock(User::class);

        $userLogin = $this->createMock(UserLogin::class);
        $userLogin->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($userLogin);

        $userId = $this->createMock(UserId::class);
        $userId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userId);

        $employeeId = $this->createMock(UserEmployeeId::class);
        $employeeId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeId);

        $profileId = $this->createMock(UserProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileId);

        $employeeMock = $this->createMock(Employee::class);

        $profileMock = $this->createMock(Profile::class);

        $profileState = $this->createMock(ProfileState::class);
        $profileState->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileState);

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($userMock, $employeeMock, $profileMock);

        $this->statefulGuard->expects(self::once())
            ->method('attempt')
            ->with([
                'user_login' => 'login',
                'password' => 'password',
                'user_id' => 1,
            ])
            ->willReturn(false);

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

        $request->expects(self::exactly(2))
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('input')
            ->with('password')
            ->willReturn('password');

        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);

        $userLogin = $this->createMock(UserLogin::class);
        $userLogin->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($userLogin);

        $userId = $this->createMock(UserId::class);
        $userId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userId);

        $employeeId = $this->createMock(UserEmployeeId::class);
        $employeeId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeId);

        $profileId = $this->createMock(UserProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileId);

        $employeeMock = $this->createMock(Employee::class);

        $profileMock = $this->createMock(Profile::class);

        $profileState = $this->createMock(ProfileState::class);
        $profileState->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($profileState);

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($userMock, $employeeMock, $profileMock);

        $this->statefulGuard->expects(self::once())
            ->method('attempt')
            ->with([
                'user_login' => 'login',
                'password' => 'password',
                'user_id' => 1,
            ])
            ->willReturn(false);

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

        $request->expects(self::exactly(2))
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

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

        $employeeMock = $this->createMock(Employee::class);
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

        $this->orchestrator->expects(self::exactly(3))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($userMock, $employeeMock, $profileMock);

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
        $this->session->expects(self::once())
            ->method('flush');

        $this->session->expects(self::once())
            ->method('invalidate')
            ->willReturn(true);

        $this->session->expects(self::once())
            ->method('regenerateToken');

        $this->statefulGuard->expects(self::once())
            ->method('logout');

        $result = $this->controller->logout();

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
