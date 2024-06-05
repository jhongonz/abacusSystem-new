<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\InstitutionController;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Institution\StoreInstitutionRequest;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionId;
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

#[CoversClass(InstitutionController::class)]
class InstitutionControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private LoggerInterface|MockObject $logger;
    private ViewFactory|MockObject $viewFactory;
    private ImageManagerInterface|MockObject $imageManager;
    private InstitutionController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->controller = new InstitutionController(
            $this->orchestrator,
            $this->imageManager,
            $this->logger,
            $this->viewFactory
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->controller,
            $this->logger,
            $this->viewFactory,
            $this->imageManager
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_index_should_return_json_response(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $request);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('institutions');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"institutions"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('institution.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_index_should_return_string(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $request);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('institutions');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"institutions"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('institution.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_changeStateInstitution_should_return_json_response_when_is_activate(): void
    {
        $requestMock = $this->createMock(Request::class);

        $institutionMock = $this->createMock(Institution::class);
        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('change-state-institution', $requestMock)
            ->willReturn($institutionMock);

        $result = $this->controller->changeStateInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_changeStateInstitution_should_return_json_response_when_is_exception(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('change-state-institution', $requestMock)
            ->willThrowException(new \Exception('Can not update institution'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not update institution');

        $result = $this->controller->changeStateInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_getInstitutions_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);

        $responseMock = $this->createMock(JsonResponse::class);
        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-institutions', $requestMock)
            ->willReturn($responseMock);

        $result = $this->controller->getInstitutions($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_getInstitution_should_return_json_response_with_id_int(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->with(['institutionId' => 1])
            ->willReturnSelf();

        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('detail-institution', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang="es"></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('institution.institution-form', [])
            ->willReturn($viewMock);

        $result = $this->controller->getInstitution($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_getInstitution_should_return_string_with_id_null(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $requestMock);

        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->with(['institutionId' => null])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('detail-institution', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang="es"></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('institution.institution-form', [])
            ->willReturn($viewMock);

        $result = $this->controller->getInstitution($requestMock);

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setLogoInstitution_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);

        $uploadFileMock = $this->createMock(UploadedFile::class);
        $uploadFileMock->expects(self::once())
            ->method('getRealPath')
            ->willReturn('localhost');

        $requestMock->expects(self::once())
            ->method('file')
            ->with('file')
            ->willReturn($uploadFileMock);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::once())
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('localhost')
            ->willReturn($imageMock);

        $result = $this->controller->setLogoInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('token', $result->getData(true));
        $this->assertArrayHasKey('url', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_storeInstitution_should_return_json_response_when_create_institution(): void
    {
        $requestMock = $this->createMock(StoreInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->withAnyParameters()
            ->willReturn(null);

        $institutionMock = $this->createMock(Institution::class);

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $institutionMock->expects(self::once())
            ->method('id')
            ->willReturn($institutionIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('create-institution', $requestMock)
            ->willReturn($institutionMock);

        $result = $this->controller->storeInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('institutionId', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_storeInstitution_should_return_json_response_with_exception_when_create_institution(): void
    {
        $requestMock = $this->createMock(StoreInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->withAnyParameters()
            ->willReturn(null);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('create-institution', $requestMock)
            ->willThrowException(new \Exception('Can not create new institution'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not create new institution');

        $result = $this->controller->storeInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_updateInstitution_should_return_json_response_when_update_institution(): void
    {
        $requestMock = $this->createMock(StoreInstitutionRequest::class);
        $requestMock->expects(self::exactly(9))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                'code',
                'name',
                'shortname',
                'phone',
                'email',
                'address',
                'observations',
                'token'
            );

        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

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
            ->with('/var/www/abacusSystem-new/public/images/tmp/token.jpg')
            ->willReturn($imageMock);

        $institutionMock = $this->createMock(Institution::class);

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $institutionMock->expects(self::once())
            ->method('id')
            ->willReturn($institutionIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('update-institution', $requestMock)
            ->willReturn($institutionMock);

        $result = $this->controller->storeInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('institutionId', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_deleteInstitution_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->with(['institutionId' => 1])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('delete-institution', $requestMock)
            ->willReturn(true);

        $result = $this->controller->deleteInstitution($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_deleteInstitution_should_return_json_response_with_exception(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->with(['institutionId' => 1])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('delete-institution', $requestMock)
            ->willThrowException(new \Exception('Can not delete institution'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not delete institution');

        $result = $this->controller->deleteInstitution($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
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
