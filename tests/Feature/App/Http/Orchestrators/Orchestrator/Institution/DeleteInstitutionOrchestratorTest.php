<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\DeleteInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\InstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteInstitutionOrchestrator::class)]
#[CoversClass(InstitutionOrchestrator::class)]
class DeleteInstitutionOrchestratorTest extends TestCase
{
    private InstitutionManagementContract|MockObject $institutionManagement;
    private DeleteInstitutionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->orchestrator = new DeleteInstitutionOrchestrator($this->institutionManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->institutionManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnTrue(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('institutionId')
            ->willReturn(1);

        $this->institutionManagement->expects(self::once())
            ->method('deleteInstitution')
            ->with(1);

        $result = $this->orchestrator->make($requestMock);
        $this->assertIsArray($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('delete-institution', $result);
    }
}
