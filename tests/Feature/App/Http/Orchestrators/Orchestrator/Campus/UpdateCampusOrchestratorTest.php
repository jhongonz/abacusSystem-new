<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\CampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\UpdateCampusOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateCampusOrchestrator::class)]
#[CoversClass(CampusOrchestrator::class)]
class UpdateCampusOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private UpdateCampusOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->orchestrator = new UpdateCampusOrchestrator($this->campusManagementMock);
    }

    protected function tearDown(): void
    {
        unset(
            $this->campusManagementMock,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_make_should_return_campus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(6))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'name',
                '123456789',
                'sandbox@test.com',
                'address',
                'observations',
                1
            );

        $campusMock = $this->createMock(Campus::class);
        $this->campusManagementMock->expects(self::once())
            ->method('updateCampus')
            ->with(1, [
                'name' => 'name',
                'phone' => '123456789',
                'email' => 'sandbox@test.com',
                'address' => 'address',
                'observations' => 'observations'
            ])
            ->willReturn($campusMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('update-campus', $result);
    }
}
