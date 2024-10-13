<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\CampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\CreateCampusOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateCampusOrchestrator::class)]
#[CoversClass(CampusOrchestrator::class)]
class CreateCampusOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private CreateCampusOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->orchestrator = new CreateCampusOrchestrator($this->campusManagementMock);
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
    public function test_make_should_create_and_return_campus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(7))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                2,
                'name',
                '123456789',
                'sandbox@local.com',
                'address',
                'observations'
            );

        $campusMock = $this->createMock(Campus::class);
        $this->campusManagementMock->expects(self::once())
            ->method('createCampus')
            ->withAnyParameters()
            ->willReturn($campusMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @return void
     */
    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('create-campus', $result);
    }
}
