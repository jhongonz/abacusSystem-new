<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Events\EventDispatcher;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\Controller;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Campus\StoreCampusRequest;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use Illuminate\Contracts\Session\Session;
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
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\DataTables;

#[CoversClass(CampusController::class)]
#[CoversClass(Controller::class)]
class CampusControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandlerMock;
    private Session|MockObject $sessionMock;
    private DataTables|MockObject $dataTablesMock;
    private EventDispatcher|MockObject $eventDispatcherMock;
    private ViewFactory|MockObject $viewFactoryMock;
    private LoggerInterface|MockObject $loggerMock;
    private CampusController $controller;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandlerMock = $this->createMock(OrchestratorHandlerContract::class);
        $this->sessionMock = $this->createMock(Session::class);
        $this->dataTablesMock = $this->createMock(DataTables::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->viewFactoryMock = $this->createMock(ViewFactory::class);

        $this->controller = new CampusController(
            $this->orchestratorHandlerMock,
            $this->sessionMock,
            $this->dataTablesMock,
            $this->eventDispatcherMock,
            $this->viewFactoryMock,
            $this->loggerMock
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->orchestratorHandlerMock,
            $this->sessionMock,
            $this->loggerMock,
            $this->viewFactoryMock,
            $this->controller,
            $this->dataTablesMock,
            $this->eventDispatcherMock
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnJsonResponse(): void
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

        $this->viewFactoryMock->expects(self::once())
            ->method('make')
            ->with('campus.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnString(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $requestMock);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('campus');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"campus"}')
            ->willReturnSelf();

        $html = '<html lang=""></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactoryMock->expects(self::once())
            ->method('make')
            ->with('campus.index')
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
    public function testGetCampusCollectionShouldReturnJsonResponse(): void
    {
        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $this->sessionMock->expects(self::once())
            ->method('get')
            ->with('employee')
            ->willReturn($employeeMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['institutionId' => 2])
            ->willReturnSelf();

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('retrieve-campus-collection', $requestMock)
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

                $this->viewFactoryMock->expects(self::once())
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

        $this->dataTablesMock->expects(self::once())
            ->method('collection')
            ->with([])
            ->willReturn($collectionDataTableMock);

        $result = $this->controller->getCampusCollection($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testGetCampusWithNullShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['campusId' => null])
            ->willReturnSelf();

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('detail-campus', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactoryMock->expects(self::once())
            ->method('make')
            ->with('campus.campus-form', [])
            ->willReturn($viewMock);

        $this->app->instance(Request::class, $requestMock);
        $result = $this->controller->getCampus($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('html', $result->getData(true));
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetCampusWithDataShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $id = 1;
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['campusId' => $id])
            ->willReturnSelf();

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('detail-campus', $requestMock)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang=""></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactoryMock->expects(self::once())
            ->method('make')
            ->with('campus.campus-form', [])
            ->willReturn($viewMock);

        $this->app->instance(Request::class, $requestMock);
        $result = $this->controller->getCampus($requestMock, $id);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('html', $result->getData(true));
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreCampusShouldReturnJsonResponseWhenCreateNewObject(): void
    {
        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $this->sessionMock->expects(self::once())
            ->method('get')
            ->with('employee')
            ->willReturn($employeeMock);

        $request = $this->createMock(StoreCampusRequest::class);
        $request->expects(self::once())
            ->method('merge')
            ->with(['institutionId' => 2])
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('filled')
            ->with('campusId')
            ->willReturn(false);

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $campusMock = $this->createMock(Campus::class);
        $campusMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('create-campus', $request)
            ->willReturn(['campus' => $campusMock]);

        $this->eventDispatcherMock->expects(self::once())
            ->method('dispatch')
            ->withAnyParameters();

        $result = $this->controller->storeCampus($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('campusId', $result->getData(true));
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreCampusShouldReturnJsonResponseWhenUpdateObject(): void
    {
        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $this->sessionMock->expects(self::once())
            ->method('get')
            ->with('employee')
            ->willReturn($employeeMock);

        $request = $this->createMock(StoreCampusRequest::class);
        $request->expects(self::once())
            ->method('merge')
            ->with(['institutionId' => 2])
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('filled')
            ->with('campusId')
            ->willReturn(true);

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $campusMock = $this->createMock(Campus::class);
        $campusMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('update-campus', $request)
            ->willReturn(['campus' => $campusMock]);

        $this->eventDispatcherMock->expects(self::once())
            ->method('dispatch')
            ->withAnyParameters();

        $result = $this->controller->storeCampus($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertArrayHasKey('campusId', $result->getData(true));
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreCampusShouldReturnExceptionWhenCreateNewObject(): void
    {
        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $this->sessionMock->expects(self::once())
            ->method('get')
            ->with('employee')
            ->willReturn($employeeMock);

        $request = $this->createMock(StoreCampusRequest::class);
        $request->expects(self::once())
            ->method('merge')
            ->with(['institutionId' => 2])
            ->willReturnSelf();

        $request->expects(self::once())
            ->method('filled')
            ->with('campusId')
            ->willReturn(false);

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('create-campus', $request)
            ->willThrowException(new \Exception('Can not create new campus'));

        $this->eventDispatcherMock->expects(self::never())
            ->method('dispatch');

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('Can not create new campus');

        $result = $this->controller->storeCampus($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testChangeStateCampusShouldInactivateAndReturnJsonResponse(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('integer')
            ->with('campusId')
            ->willReturn(1);

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('change-state-campus', $request);

        $result = $this->controller->changeStateCampus($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testChangeStateCampusShouldReturnException(): void
    {
        $request = $this->createMock(Request::class);

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('change-state-campus', $request)
            ->willThrowException(new \Exception('Its could not update module'));

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('Its could not update module');

        $result = $this->controller->changeStateCampus($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteCampusShouldReturnJsonResponse(): void
    {
        $campusId = 1;

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['campusId' => $campusId])
            ->willReturnSelf();

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('delete-campus', $requestMock)
            ->willReturn([]);

        $result = $this->controller->deleteCampus($requestMock, $campusId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteCampusShouldReturnException(): void
    {
        $campusId = 1;

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['campusId' => $campusId])
            ->willReturnSelf();

        $this->orchestratorHandlerMock->expects(self::once())
            ->method('handler')
            ->with('delete-campus', $requestMock)
            ->willThrowException(new \Exception('Its could not delete object'));

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('Its could not delete object');

        $result = $this->controller->deleteCampus($requestMock, $campusId);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    public function testMiddlewareShouldReturnObject(): void
    {
        $dataExpected = [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getCampusCollection', 'deleteCampus', 'changeStateCampus', 'storeCampus', 'getCampus',
            ]),
        ];
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Middleware::class, $result);
        $this->assertEquals($dataExpected, $result);
    }
}
