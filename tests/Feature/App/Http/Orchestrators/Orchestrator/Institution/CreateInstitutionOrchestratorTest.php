<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\CreateInstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateInstitutionOrchestrator::class)]
class CreateInstitutionOrchestratorTest extends TestCase
{
    private InstitutionManagementContract|MockObject $institutionManagement;
    private ImageManagerInterface|MockObject $imageManager;
    private CreateInstitutionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->orchestrator = new CreateInstitutionOrchestrator(
            $this->institutionManagement,
            $this->imageManager
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->institutionManagement,
            $this->orchestrator,
            $this->imageManager
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_institution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(6))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                null,
                'name',
                'code',
                'shortname',
                'observations',
                'token'
            );

        $requestMock->expects(self::once())
            ->method('filled')
            ->with('token')
            ->willReturn(true);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('/var/www/abacusSystem-new/public/images/tmp/token.jpg')
            ->willReturn($imageMock);

        $institutionMock = $this->createMock(Institution::class);
        $this->institutionManagement->expects(self::once())
            ->method('createInstitution')
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
        $this->assertSame('create-institution', $result);
    }
}
