<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\DetailInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\InstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DetailInstitutionOrchestrator::class)]
#[CoversClass(InstitutionOrchestrator::class)]
class DetailInstitutionOrchestratorTest extends TestCase
{
    private InstitutionManagementContract|MockObject $institutionManagement;
    private DetailInstitutionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->orchestrator = new DetailInstitutionOrchestrator($this->institutionManagement);
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
    public function testMakeShouldReturnArrayWithInstitution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('institutionId')
            ->willReturn(1);

        $institutionMock = $this->createMock(Institution::class);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn('logo.jpg');
        $institutionMock->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['institutionId']);
        $this->assertSame($institutionMock, $result['institution']);
        $this->assertIsString($result['image']);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithoutInstitution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('institutionId')
            ->willReturn(0);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(0)
            ->willReturn(null);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertIsInt($result['institutionId']);
        $this->assertEquals(0, $result['institutionId']);
        $this->assertNull($result['institution']);
        $this->assertNull($result['image']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('detail-institution', $result);
    }
}
