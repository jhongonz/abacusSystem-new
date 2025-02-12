<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Events\EventDispatcher;
use App\Http\Controllers\Controller;
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

#[CoversClass(InstitutionController::class)]
#[CoversClass(Controller::class)]
class InstitutionControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private DataTables|MockObject $datatables;
    private ImageManagerInterface|MockObject $imageManager;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private EventDispatcher|MockObject $dispatcher;
    private InstitutionController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->datatables = $this->createMock(DataTables::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->controller = new InstitutionController(
            $this->orchestrator,
            $this->datatables,
            $this->dispatcher,
            $this->imageManager,
            $this->viewFactory,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->controller,
            $this->logger,
            $this->viewFactory,
            $this->imageManager,
            $this->datatables,
            $this->dispatcher
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
    public function testChangeStateInstitutionShouldReturnJsonResponseWhenIsActivate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->willReturn(10);

        $institutionMock = $this->createMock(Institution::class);
        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('change-state-institution', $requestMock)
            ->willReturn(['institution' => $institutionMock]);

        $this->dispatcher->expects(self::once())
            ->method('dispatch')
            ->withAnyParameters();

        $result = $this->controller->changeStateInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testChangeStateInstitutionShouldReturnJsonResponseWhenIsException(): void
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
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function testGetInstitutionsShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-institutions', $requestMock)
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

        $responseMock = $this->createMock(JsonResponse::class);
        $collectionDataTableMock->expects(self::once())
            ->method('toJson')
            ->willReturn($responseMock);

        $this->datatables->expects(self::once())
            ->method('collection')
            ->with([])
            ->willReturn($collectionDataTableMock);

        $result = $this->controller->getInstitutions($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testGetInstitutionShouldReturnJsonResponseWithIdInt(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
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
    public function testGetInstitutionShouldReturnStringWithIdNull(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $requestMock);

        $requestMock->expects(self::once())
            ->method('merge')
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
    public function testSetLogoInstitutionShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);

        $uploadFileMock = $this->createMock(UploadedFile::class);
        $uploadFileMock->expects(self::once())
            ->method('isValid')
            ->willReturn(true);

        $uploadFileMock->expects(self::once())
            ->method('getRealPath')
            ->willReturn('localhost');

        $requestMock->expects(self::once())
            ->method('file')
            ->with('file')
            ->willReturn($uploadFileMock);

        Str::createRandomStringsUsing(function () {
            return '248ec6063c';
        });

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
    public function testSetLogoInstitutionShouldReturnInternalError(): void
    {
        $request = $this->createMock(Request::class);

        $uploadFileMock = $this->createMock(\stdClass::class);
        $request->expects(self::once())
            ->method('file')
            ->with('file')
            ->willReturn($uploadFileMock);

        $result = $this->controller->setLogoInstitution($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testSetLogoInstitutionShouldReturnInternalErrorWhenObjectIsNotValid(): void
    {
        $request = $this->createMock(Request::class);

        $result = $this->controller->setLogoInstitution($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testStoreInstitutionShouldReturnJsonResponseWhenCreateInstitution(): void
    {
        $requestMock = $this->createMock(StoreInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('institutionId')
            ->willReturn(null);

        $institutionMock = $this->createMock(Institution::class);

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $institutionIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $institutionMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($institutionIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('create-institution', $requestMock)
            ->willReturn(['institution' => $institutionMock]);

        $result = $this->controller->storeInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('institutionId', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testStoreInstitutionShouldReturnJsonResponseWithExceptionWhenCreateInstitution(): void
    {
        $requestMock = $this->createMock(StoreInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('institutionId')
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
    public function testUpdateInstitutionShouldReturnJsonResponseWhenUpdateInstitution(): void
    {
        $requestMock = $this->createMock(StoreInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('institutionId')
            ->willReturn(1);

        $institutionMock = $this->createMock(Institution::class);

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $institutionIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $institutionMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($institutionIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('update-institution', $requestMock)
            ->willReturn(['institution' => $institutionMock]);

        $result = $this->controller->storeInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('institutionId', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testDeleteInstitutionShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['institutionId' => 1])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('delete-institution', $requestMock)
            ->willReturn([]);

        $result = $this->controller->deleteInstitution($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteInstitutionShouldReturnJsonResponseWithException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
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

    public function testMiddlewareShouldReturnObject(): void
    {
        $dataExpected = [
            new Middleware(['auth', 'verify-session']),
        ];
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(Middleware::class, $result);
        $this->assertEquals($dataExpected, $result);
    }
}
