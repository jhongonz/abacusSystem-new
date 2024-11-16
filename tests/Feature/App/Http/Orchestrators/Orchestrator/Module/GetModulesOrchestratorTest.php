<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\GetModulesOrchestrator;
use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
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

#[CoversClass(GetModulesOrchestrator::class)]
class GetModulesOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private ModuleDataTransformerContract|MockObject $moduleDataTransformer;
    private DataTables|MockObject $dataTables;
    private ViewFactory|MockObject $viewFactory;
    private GetModulesOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->moduleDataTransformer = $this->createMock(ModuleDataTransformerContract::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->dataTables = $this->createMock(DataTables::class);
        $this->orchestrator = new GetModulesOrchestrator(
            $this->moduleManagement,
            $this->moduleDataTransformer,
            $this->dataTables,
            $this->viewFactory
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->moduleManagement,
            $this->orchestrator,
            $this->dataTables,
            $this->moduleDataTransformer,
            $this->viewFactory
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

        $moduleMock = $this->createMock(Module::class);
        $modulesMock = new Modules([$moduleMock]);

        $this->moduleManagement->expects(self::once())
            ->method('searchModules')
            ->with([])
            ->willReturn($modulesMock);

        $this->moduleDataTransformer->expects(self::once())
            ->method('write')
            ->with($moduleMock)
            ->willReturnSelf();

        $this->moduleDataTransformer->expects(self::once())
            ->method('readToShare')
            ->willReturn([]);

        $collectionDatatableMock = $this->createMock(CollectionDataTable::class);
        $collectionDatatableMock->expects(self::once())
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

                    $view = $closure(['id' => 1, 'state' => 2]);
                    $this->assertIsString($view);
                    $this->assertSame('<html lang="es"></html>', $view);

                    return true;
                })
            )
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
        $this->assertSame('retrieve-modules', $result);
    }
}
