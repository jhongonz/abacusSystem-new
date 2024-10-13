<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\CampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\DetailCampusOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DetailCampusOrchestrator::class)]
#[CoversClass(CampusOrchestrator::class)]
class DetailCampusOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private DetailCampusOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->orchestrator = new DetailCampusOrchestrator($this->campusManagementMock);
    }

    protected function tearDown(): void
    {
        unset(
            $this->campusManagementMock,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_make_should_return_array_with_campus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('campusId')
            ->willReturn(1);

        $campusMock = $this->createMock(Campus::class);
        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusById')
            ->with(1)
            ->willReturn($campusMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('campusId', $result);
        $this->assertArrayHasKey('campus', $result);
        $this->assertInstanceOf(Campus::class, $result['campus']);
        $this->assertSame($campusMock, $result['campus']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_make_should_return_array_with_null(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('campusId')
            ->willReturn(null);

        $this->campusManagementMock->expects(self::never())
            ->method('searchCampusById');

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('campusId', $result);
        $this->assertArrayHasKey('campus', $result);
        $this->assertNull($result['campus']);
        $this->assertNull($result['campusId']);
    }

    /**
     * @return void
     */
    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('detail-campus', $result);
    }
}
