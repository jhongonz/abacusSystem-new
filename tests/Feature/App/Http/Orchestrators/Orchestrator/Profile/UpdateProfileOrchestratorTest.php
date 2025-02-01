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
            ['id' => 2],
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

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('profileId')
            ->willReturn(1);

        $dataExpected = [
            'name' => 'name',
            'description' => 'description',
            'modules' => [1, 2],
        ];

        $profileMock = $this->createMock(Profile::class);
        $this->profileManagement->expects(self::once())
            ->method('updateProfile')
            ->with(1, $dataExpected)
            ->willReturn($profileMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('profile', $result);
        $this->assertInstanceOf(Profile::class, $result['profile']);
        $this->assertSame($profileMock, $result['profile']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('update-profile', $result);
    }
}
