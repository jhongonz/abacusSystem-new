<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\GetInstitutionsOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
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

#[CoversClass(GetInstitutionsOrchestrator::class)]
class GetInstitutionsOrchestratorTest extends TestCase
{
    private InstitutionDataTransformerContract|MockObject $institutionDataTransformer;
    private InstitutionManagementContract|MockObject $institutionManagement;
    private DataTables|MockObject $dataTables;
    private ViewFactory|MockObject $viewFactory;
    private GetInstitutionsOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->institutionDataTransformer = $this->createMock(InstitutionDataTransformerContract::class);
        $this->dataTables = $this->createMock(DataTables::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);

        $this->orchestrator = new GetInstitutionsOrchestrator(
            $this->institutionManagement,
            $this->institutionDataTransformer,
            $this->dataTables,
            $this->viewFactory
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->viewFactory,
            $this->dataTables,
            $this->institutionManagement,
            $this->institutionDataTransformer
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function test_make_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $institutionMock = $this->createMock(Institution::class);
        $institutions = new Institutions([$institutionMock]);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutions')
            ->with([])
            ->willReturn($institutions);

        $this->institutionDataTransformer->expects(self::once())
            ->method('write')
            ->with($institutionMock)
            ->willReturnSelf();

        $this->institutionDataTransformer->expects(self::once())
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

                $view = $closure(['id' => 1,'state' => 2]);
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

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-institutions', $result);
    }
}
