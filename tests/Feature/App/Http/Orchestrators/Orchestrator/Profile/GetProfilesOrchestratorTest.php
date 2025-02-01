<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\GetProfilesOrchestrator;
use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetProfilesOrchestrator::class)]
class GetProfilesOrchestratorTest extends TestCase
{
    private ProfileDataTransformerContract|MockObject $profileDataTransformer;
    private ProfileManagementContract|MockObject $profileManagement;
    private GetProfilesOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileDataTransformer = $this->createMock(ProfileDataTransformerContract::class);
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new GetProfilesOrchestrator(
            $this->profileManagement,
            $this->profileDataTransformer,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->profileDataTransformer,
            $this->profileManagement
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

        $profileMock = $this->createMock(Profile::class);
        $profileMock2 = $this->createMock(Profile::class);
        $profilesMock = new Profiles([$profileMock, $profileMock2]);

        $this->profileManagement->expects(self::once())
            ->method('searchProfiles')
            ->with([])
            ->willReturn($profilesMock);

        $this->profileDataTransformer->expects(self::exactly(2))
            ->method('write')
            ->with($profileMock)
            ->willReturnSelf();

        $this->profileDataTransformer->expects(self::exactly(2))
            ->method('readToShare')
            ->willReturnOnConsecutiveCalls(['sandbox' => 'testing'], ['sandbox2' => 'testing']);

        $result = $this->orchestrator->make($requestMock);

        $dataExpected = [['sandbox' => 'testing'], ['sandbox2' => 'testing']];
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($dataExpected, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-profiles', $result);
    }
}
