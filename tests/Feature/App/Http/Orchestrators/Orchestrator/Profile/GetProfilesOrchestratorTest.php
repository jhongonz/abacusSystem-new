<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\GetProfilesOrchestrator;
use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
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

#[CoversClass(GetProfilesOrchestrator::class)]
class GetProfilesOrchestratorTest extends TestCase
{
    private ViewFactory|MockObject $viewFactory;
    private DataTables|MockObject $dataTables;
    private ProfileDataTransformerContract|MockObject $profileDataTransformer;
    private ProfileManagementContract|MockObject $profileManagement;
    private GetProfilesOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->dataTables = $this->createMock(DataTables::class);
        $this->profileDataTransformer = $this->createMock(ProfileDataTransformerContract::class);
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new GetProfilesOrchestrator(
            $this->profileManagement,
            $this->viewFactory,
            $this->dataTables,
            $this->profileDataTransformer
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->dataTables,
            $this->profileDataTransformer,
            $this->viewFactory,
            $this->profileManagement
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

        $profileMock = $this->createMock(Profile::class);
        $profilesMock = new Profiles([$profileMock]);

        $this->profileManagement->expects(self::once())
            ->method('searchProfiles')
            ->with([])
            ->willReturn($profilesMock);

        $this->profileDataTransformer->expects(self::once())
            ->method('write')
            ->with($profileMock)
            ->willReturnSelf();

        $this->profileDataTransformer->expects(self::once())
            ->method('readToShare')
            ->willReturn([]);

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
                    $this->assertSame($view, '<html lang="es"></html>');

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

        $collection = new Collection([[]]);
        $this->dataTables->expects(self::once())
            ->method('collection')
            ->with($collection)
            ->willReturn($collectionDataTableMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-profiles', $result);
    }
}
