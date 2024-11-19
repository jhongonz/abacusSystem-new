<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\GetInstitutionsOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\InstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetInstitutionsOrchestrator::class)]
#[CoversClass(InstitutionOrchestrator::class)]
class GetInstitutionsOrchestratorTest extends TestCase
{
    private InstitutionDataTransformerContract|MockObject $institutionDataTransformer;
    private InstitutionManagementContract|MockObject $institutionManagement;
    private GetInstitutionsOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->institutionDataTransformer = $this->createMock(InstitutionDataTransformerContract::class);

        $this->orchestrator = new GetInstitutionsOrchestrator(
            $this->institutionManagement,
            $this->institutionDataTransformer
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->institutionManagement,
            $this->institutionDataTransformer
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnJsonResponse(): void
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

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-institutions', $result);
    }
}
