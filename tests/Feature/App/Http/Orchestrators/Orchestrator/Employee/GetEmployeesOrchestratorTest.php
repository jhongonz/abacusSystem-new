<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\GetEmployeesOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\DataTables;

#[CoversClass(GetEmployeesOrchestrator::class)]
class GetEmployeesOrchestratorTest extends TestCase
{
    private EmployeeDataTransformerContract|MockObject $employeeDataTransformer;
    private DataTables|MockObject $dataTables;
    private ViewFactory|MockObject $viewFactory;
    private EmployeeManagementContract|MockObject $employeeManagement;
    private GetEmployeesOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->dataTables = $this->createMock(DataTables::class);
        $this->employeeDataTransformer = $this->createMock(EmployeeDataTransformerContract::class);
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);

        $this->orchestrator = new GetEmployeesOrchestrator(
            $this->employeeManagement,
            $this->employeeDataTransformer,
            $this->dataTables,
            $this->viewFactory
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->dataTables,
            $this->employeeManagement,
            $this->employeeDataTransformer,
            $this->viewFactory,
            $this->orchestrator,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function testMakeShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $employeesMock = new Employees();
        $employeeMock = $this->createMock(Employee::class);
        $employeesMock->addItem($employeeMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployees')
            ->with([])
            ->willReturn($employeesMock);

        $this->employeeDataTransformer->expects(self::once())
            ->method('write')
            ->with($employeeMock)
            ->willReturnSelf();

        $this->employeeDataTransformer->expects(self::once())
            ->method('readToShare')
            ->willReturn([]);

        $collectionDatatableMock = $this->createMock(CollectionDataTable::class);
        $collectionDatatableMock->expects(self::once())
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

        $collectionDatatableMock->expects(self::once())
            ->method('escapeColumns')
            ->with([])
            ->willReturnSelf();

        $responseMock = $this->createMock(JsonResponse::class);
        $collectionDatatableMock->expects(self::once())
            ->method('toJson')
            ->willReturn($responseMock);

        $collection = new Collection([[]]);
        $this->dataTables->expects(self::once())
            ->method('collection')
            ->with($collection)
            ->willReturn($collectionDatatableMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-employees', $result);
    }
}
