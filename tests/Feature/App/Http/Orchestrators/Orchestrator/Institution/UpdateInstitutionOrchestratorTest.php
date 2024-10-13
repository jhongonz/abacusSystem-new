<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\UpdateInstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateInstitutionOrchestrator::class)]
class UpdateInstitutionOrchestratorTest extends TestCase
{
    private InstitutionManagementContract|MockObject $institutionManagement;
    private ImageManagerInterface|MockObject $imageManagerMock;
    private UpdateInstitutionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->imageManagerMock = $this->createMock(ImageManagerInterface::class);
        $this->orchestrator = new UpdateInstitutionOrchestrator($this->institutionManagement, $this->imageManagerMock);
    }

    public function tearDown(): void
    {
        unset(
            $this->institutionManagement,
            $this->imageManagerMock,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_institution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(9))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'code',
                'name',
                'shortname',
                'phone',
                'email',
                'address',
                'osbservations',
                null,
                1
            );

        $institutionMock = $this->createMock(Institution::class);
        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
            ->willReturn($institutionMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('update-institution', $result);
    }
}
