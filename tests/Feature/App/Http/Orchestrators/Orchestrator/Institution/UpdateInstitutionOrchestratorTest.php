<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\InstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\UpdateInstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateInstitutionOrchestrator::class)]
#[CoversClass(InstitutionOrchestrator::class)]
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
    public function testMakeShouldReturnInstitution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(7))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'code',
                'name',
                'shortname',
                'phone',
                'email',
                'address',
                'observations'
            );

        $requestMock->expects(self::once())
            ->method('filled')
            ->with('token')
            ->willReturn(true);

        $requestMock->expects(self::once())
            ->method('string')
            ->with('token')
            ->willReturn('token');

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('institutionId')
            ->willReturn(1);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $this->imageManagerMock->expects(self::once())
            ->method('read')
            ->with('/var/www/abacusSystem-new/public/images/tmp/token.jpg')
            ->willReturn($imageMock);

        $institutionMock = $this->createMock(Institution::class);
        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
            ->willReturn($institutionMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('institution', $result);
        $this->assertInstanceOf(Institution::class, $result['institution']);
        $this->assertSame($institutionMock, $result['institution']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('update-institution', $result);
    }
}
