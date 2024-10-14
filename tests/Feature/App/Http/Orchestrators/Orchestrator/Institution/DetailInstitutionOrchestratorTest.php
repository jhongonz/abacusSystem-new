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
    public function test_make_should_return_array_with_institution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
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
    public function test_make_should_return_array_without_institution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('institutionId')
            ->willReturn(null);

        $this->institutionManagement->expects(self::never())
            ->method('searchInstitutionById');

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertNull($result['institutionId']);
        $this->assertNull($result['institution']);
        $this->assertNull($result['image']);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('detail-institution', $result);
    }
}
