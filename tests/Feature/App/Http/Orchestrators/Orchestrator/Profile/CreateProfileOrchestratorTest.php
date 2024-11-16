<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\CreateProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\ProfileOrchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateProfileOrchestrator::class)]
#[CoversClass(ProfileOrchestrator::class)]
class CreateProfileOrchestratorTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagement;
    private CreateProfileOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new CreateProfileOrchestrator($this->profileManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->profileManagement,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldCreateAndReturnProfile(): void
    {
        $modulesExpected = [
            ['id' => 1],
        ];

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(3))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'name',
                'description',
                $modulesExpected
            );

        $profileMock = $this->createMock(Profile::class);
        $this->profileManagement->expects(self::once())
            ->method('createProfile')
            ->withAnyParameters()
            ->willReturn($profileMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('create-profile', $result);
    }
}
