<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\InstitutionController;
use App\Http\Requests\Institution\StoreInstitutionRequest;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionState;
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

#[CoversClass(InstitutionController::class)]
class InstitutionControllerTest extends TestCase
{
    private InstitutionDataTransformerContract|MockObject $dataTransformer;
    private InstitutionManagementContract|MockObject $institutionManagement;
    private DataTables|MockObject $dataTable;
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
        $this->dataTransformer = $this->createMock(InstitutionDataTransformerContract::class);
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->dataTable = $this->createMock(DataTables::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->controller = new InstitutionController(
            $this->dataTransformer,
            $this->institutionManagement,
            $this->dataTable,
            $this->imageManager,
            $this->logger,
            $this->viewFactory
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->controller,
            $this->dataTransformer,
            $this->institutionManagement,
            $this->dataTable,
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
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->with('id')
            ->willReturnOnConsecutiveCalls(1, 1);

        $institutionMock = $this->createMock(Institution::class);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
            ->willReturn($institutionMock);

        $result = $this->controller->changeStateInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_changeStateInstitution_should_return_json_response_when_is_inactivate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->with('id')
            ->willReturnOnConsecutiveCalls(1, 1);

        $institutionMock = $this->createMock(Institution::class);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('inactive')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(3);

        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
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
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->with('id')
            ->willReturnOnConsecutiveCalls(1, 1);

        $institutionMock = $this->createMock(Institution::class);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('inactive')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(3);

        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
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
    public function test_getInstitutions_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $institutionMock = $this->createMock(Institution::class);
        $institutionsMock = new Institutions;
        $institutionsMock->addItem($institutionMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutions')
            ->with([])
            ->willReturn($institutionsMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($institutionMock)
            ->willReturnSelf();

        $dataExpected = ['hello' => 'word'];
        $this->dataTransformer->expects(self::once())
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

        $responseMock = $this->createMock(JsonResponse::class);
        $collectionDataTableMock->expects(self::once())
            ->method('toJson')
            ->willReturn($responseMock);

        $this->dataTable->expects(self::once())
            ->method('collection')
            ->with($collectionMock)
            ->willReturn($collectionDataTableMock);

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
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $institutionMock = $this->createMock(Institution::class);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn('logo.jpg');
        $institutionMock->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::exactly(3))
            ->method('with')
            ->withAnyParameters()
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('institution.institution-form')
            ->willReturn($viewMock);

        $result = $this->controller->getInstitution(1);

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

        $this->institutionManagement->expects(self::never())
            ->method('searchInstitutionById');

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::exactly(3))
            ->method('with')
            ->withAnyParameters()
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('institution.institution-form')
            ->willReturn($viewMock);

        $result = $this->controller->getInstitution();

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
        $requestMock->expects(self::exactly(6))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                null,
                'name',
                'code',
                'shortname',
                'observations',
                'token'
            );

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
        $this->institutionManagement->expects(self::once())
            ->method('createInstitution')
            ->withAnyParameters()
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
        $requestMock->expects(self::exactly(6))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                null,
                'name',
                'code',
                'shortname',
                'observations',
                'token'
            );

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
            ->withAnyParameters()
            ->willReturn($imageMock);

        $this->institutionManagement->expects(self::once())
            ->method('createInstitution')
            ->withAnyParameters()
            ->willThrowException(new \Exception('Can not create institution'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not create institution');

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
                'token',
            );

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

        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters();

        $result = $this->controller->storeInstitution($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
        $this->assertArrayHasKey('institutionId', $result->getData(true));
    }

    public function test_deleteInstitution_should_return_json_response(): void
    {
        $this->institutionManagement->expects(self::once())
            ->method('deleteInstitution')
            ->with(1);

        $result = $this->controller->deleteInstitution(1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    public function test_deleteInstitution_should_return_json_response_with_exception(): void
    {
        $this->institutionManagement->expects(self::once())
            ->method('deleteInstitution')
            ->with(1)
            ->willThrowException(new \Exception('Can not delete institution'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not delete institution');

        $result = $this->controller->deleteInstitution(1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }
}
