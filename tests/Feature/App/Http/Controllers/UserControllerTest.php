<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\User\RecoveryAccountRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\User\Domain\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\Factory as ViewFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(UserController::class)]
#[CoversClass(Controller::class)]
class UserControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private Hasher|MockObject $hasher;
    private UrlGenerator|MockObject $urlGenerator;
    private UserController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->hasher = $this->createMock(Hasher::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->urlGenerator = $this->createMock(UrlGenerator::class);
        $this->controller = new UserController(
            $this->orchestrator,
            $this->hasher,
            $this->viewFactory,
            $this->logger,
            $this->urlGenerator
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->controller,
            $this->viewFactory,
            $this->logger,
            $this->orchestrator,
            $this->hasher,
            $this->urlGenerator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnJsonResponse(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $request);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('users');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"users"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('user.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnString(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $request);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('users');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"users"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('user.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
    }

    /**
     * @throws Exception
     */
    public function testRecoveryAccountShouldReturnJsonResponse(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $request);

        $html = '<html lang="es"></html>';
        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('user.recovery-account')
            ->willReturn($viewMock);

        $result = $this->controller->recoveryAccount();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testRecoveryAccountShouldReturnString(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $request);

        $html = '<html lang="es"></html>';
        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('user.recovery-account')
            ->willReturn($viewMock);

        $result = $this->controller->recoveryAccount();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
    }

    /**
     * @throws Exception
     */
    public function testValidateAccountShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(RecoveryAccountRequest::class);

        $employeeMock = $this->createMock(Employee::class);

        $userIdMock = $this->createMock(EmployeeUserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-employee', $requestMock)
            ->willReturn($employeeMock);

        $link = 'http://localhost/reset-account/1';
        $this->urlGenerator->expects(self::once())
            ->method('route')
            ->with('user.reset-account', ['id' => 1])
            ->willReturn($link);

        $result = $this->controller->validateAccount($requestMock);

        $dataResult = $result->getData(true);
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('link', $dataResult);
        $this->assertSame($link, $dataResult['link']);
    }

    /**
     * @throws Exception
     */
    public function testResetAccountShouldReturnResponse(): void
    {
        $view = $this->createMock(View::class);

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('user.restart-password', [
                'userId' => 1,
                'activeLink' => true,
            ])
            ->willReturn($view);

        $result = $this->controller->resetAccount(1);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($html, $result->content());
    }

    /**
     * @throws Exception
     */
    public function testResetPasswordShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(ResetPasswordRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('password')
            ->willReturn('password');

        $requestMock->expects(self::once())
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->hasher->expects(self::once())
            ->method('make')
            ->with('password')
            ->willReturn('password');

        $userMock = $this->createMock(User::class);
        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('update-user', $requestMock)
            ->willReturn($userMock);

        $result = $this->controller->resetPassword($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    public function testMiddlewareShouldReturnObject(): void
    {
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Middleware::class, $result);
    }
}
