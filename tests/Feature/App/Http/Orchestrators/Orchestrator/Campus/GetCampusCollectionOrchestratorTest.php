<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\GetCampusCollectionOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
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

#[CoversClass(GetCampusCollectionOrchestrator::class)]
#[CoversClass(CampusCollection::class)]
class GetCampusCollectionOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private CampusDataTransformerContract|MockObject $campusDataTransformerMock;
    private DataTables|MockObject $dataTablesMock;
    private ViewFactory|MockObject $viewFactoryMock;
    private GetCampusCollectionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->campusDataTransformerMock = $this->createMock(CampusDataTransformerContract::class);
        $this->dataTablesMock = $this->createMock(DataTables::class);
        $this->viewFactoryMock = $this->createMock(ViewFactory::class);
        $this->orchestrator = new GetCampusCollectionOrchestrator(
            $this->campusManagementMock,
            $this->campusDataTransformerMock,
            $this->dataTablesMock,
            $this->viewFactoryMock
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->campusManagementMock,
            $this->campusDataTransformerMock,
            $this->dataTablesMock,
            $this->viewFactoryMock,
            $this->orchestrator
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
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(1, []);

        $campusMock = $this->createMock(Campus::class);
        $campusCollectionMock = new CampusCollection([$campusMock]);

        $this->campusDataTransformerMock->expects(self::once())
            ->method('write')
            ->with($campusMock)
            ->willReturnSelf();

        $this->campusDataTransformerMock->expects(self::once())
            ->method('readToShare')
            ->willReturn([]);

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusCollection')
            ->with(1, [])
            ->willReturn($campusCollectionMock);

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

        $collectionMock = new Collection([[]]);
        $this->dataTablesMock->expects(self::once())
            ->method('collection')
            ->with($collectionMock)
            ->willReturn($collectionDataTableMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-campus-collection', $result);
    }
}
