<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\CreateInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\InstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

#[CoversClass(CreateInstitutionOrchestrator::class)]
#[CoversClass(InstitutionOrchestrator::class)]
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
    public function testMakeShouldReturnInstitution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(7))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'name',
                'code',
                'shortname',
                'observations',
                'address',
                'phone',
                'email',
            );

        $requestMock->expects(self::once())
            ->method('string')
            ->willReturn('token');

        $requestMock->expects(self::once())
            ->method('filled')
            ->with('token')
            ->willReturn(true);

        Str::createUuidsUsing(function () {
            return Uuid::fromString('eadbfeac-5258-45c2-bab7-ccb9b5ef74f9');
        });

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

        $dataInstitutionExpected = [
            'id' => null,
            'name' => 'name',
            'code' => 'code',
            'shortname' => 'shortname',
            'observations' => 'observations',
            'address' => 'address',
            'phone' => 'phone',
            'email' => 'email',
            'state' => 1,
            'logo' => 'eadbfeac-5258-45c2-bab7-ccb9b5ef74f9.jpg',
        ];

        $institutionMock = $this->createMock(Institution::class);
        $this->institutionManagement->expects(self::once())
            ->method('createInstitution')
            ->with([Institution::TYPE => $dataInstitutionExpected])
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
        $this->assertSame('create-institution', $result);
    }
}
