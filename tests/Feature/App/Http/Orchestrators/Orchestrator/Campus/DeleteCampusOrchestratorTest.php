<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\CampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\DeleteCampusOrchestrator;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteCampusOrchestrator::class)]
#[CoversClass(CampusOrchestrator::class)]
class DeleteCampusOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private DeleteCampusOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->orchestrator = new DeleteCampusOrchestrator($this->campusManagementMock);
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
     * @throws Exception
     */
    public function testMakeShouldDeleteCampusShouldReturnTrue(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('campusId')
            ->willReturn(1);

        $this->campusManagementMock->expects(self::once())
            ->method('deleteCampus')
            ->with(1);

        $result = $this->orchestrator->make($requestMock);
        $this->assertTrue($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('delete-campus', $result);
    }
}
