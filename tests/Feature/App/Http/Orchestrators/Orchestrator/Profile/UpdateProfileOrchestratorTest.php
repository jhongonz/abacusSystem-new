<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\ProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\UpdateProfileOrchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateProfileOrchestrator::class)]
#[CoversClass(ProfileOrchestrator::class)]
class UpdateProfileOrchestratorTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagement;
    private UpdateProfileOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new UpdateProfileOrchestrator($this->profileManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->profileManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldUpdateAndReturnProfile(): void
    {
        $modulesExpected = [
            ['id' => 1],
        ];

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(4))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'name',
                'description',
                $modulesExpected,
                1
            );

        $profileMock = $this->createMock(Profile::class);
        $this->profileManagement->expects(self::once())
            ->method('updateProfile')
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
        $this->assertSame('update-profile', $result);
    }
}
