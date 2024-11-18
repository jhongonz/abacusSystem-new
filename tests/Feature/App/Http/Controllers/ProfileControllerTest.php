<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Profile\StoreProfileRequest;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\Factory as ViewFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Yajra\DataTables\DataTables;

#[CoversClass(ProfileController::class)]
#[CoversClass(Controller::class)]
class ProfileControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private DataTables|MockObject $dataTables;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private ProfileController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->dataTables = $this->createMock(DataTables::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->controller = new ProfileController(
            $this->orchestratorHandler,
            $this->dataTables,
            $this->viewFactory,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->controller,
            $this->orchestratorHandler,
            $this->viewFactory,
            $this->logger,
            $this->dataTables
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
            ->willReturn('profiles');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"profiles"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('profile.index')
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
            ->willReturn('profiles');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"profiles"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('profile.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testGetProfilesShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);

        $responseMock = $this->createMock(JsonResponse::class);
        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('retrieve-profiles', $requestMock)
            ->willReturn($responseMock);

        $result = $this->controller->getProfiles($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testChangeStateProfileShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $profileMock = $this->createMock(Profile::class);

        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('change-state-profile', $requestMock)
            ->willReturn($profileMock);

        $result = $this->controller->changeStateProfile($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testChangeStateProfileShouldReturnJsonResponseWhenIsException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $profileMock = $this->createMock(Profile::class);

        $profileMock->expects(self::never())
            ->method('id');

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('change-state-profile', $requestMock)
            ->willThrowException(new \Exception('Can not change state profile'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not change state profile');

        $result = $this->controller->changeStateProfile($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteProfileShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['profileId' => 1])
            ->willReturnSelf();

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('delete-profile', $requestMock)
            ->willReturn(true);

        $result = $this->controller->deleteProfile($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteProfileShouldReturnJsonResponseWhenIsException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['profileId' => 1])
            ->willReturnSelf();

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('delete-profile', $requestMock)
            ->willThrowException(new \Exception('Can not delete profile'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not delete profile');

        $result = $this->controller->deleteProfile($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetProfileShouldReturnJsonResponseWhenIdIsNull(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['profileId' => null])
            ->willReturnSelf();

        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('detail-profile', $requestMock)
            ->willreturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang="es"></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('profile.profile-form', [])
            ->willReturn($viewMock);

        $result = $this->controller->getProfile($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testStoreProfileShouldCreateProfileWhenIdIsNull(): void
    {
        $requestMock = $this->createMock(StoreProfileRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(null);

        $profileMock = $this->createMock(Profile::class);

        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('create-profile')
            ->willReturn($profileMock);

        $result = $this->controller->storeProfile($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreProfileShouldUpdateProfileWhenIdIsNotNull(): void
    {
        $requestMock = $this->createMock(StoreProfileRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(1);

        $profileMock = $this->createMock(Profile::class);

        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('update-profile')
            ->willReturn($profileMock);

        $result = $this->controller->storeProfile($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreProfileShouldReturnException(): void
    {
        $requestMock = $this->createMock(StoreProfileRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(null);

        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('create-profile')
            ->willThrowException(new \Exception('Can not create new profile'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not create new profile');

        $result = $this->controller->storeProfile($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    public function testMiddlewareShouldReturnObject(): void
    {
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Middleware::class, $result);
    }
}
