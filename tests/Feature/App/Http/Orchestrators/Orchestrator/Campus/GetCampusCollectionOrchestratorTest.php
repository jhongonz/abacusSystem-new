<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\GetCampusCollectionOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetCampusCollectionOrchestrator::class)]
#[CoversClass(CampusCollection::class)]
class GetCampusCollectionOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private CampusDataTransformerContract|MockObject $campusDataTransformerMock;
    private GetCampusCollectionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->campusDataTransformerMock = $this->createMock(CampusDataTransformerContract::class);
        $this->orchestrator = new GetCampusCollectionOrchestrator(
            $this->campusManagementMock,
            $this->campusDataTransformerMock,
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->campusManagementMock,
            $this->campusDataTransformerMock,
            $this->orchestrator
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
            ->method('integer')
            ->with('institutionId')
            ->willReturn(1);

        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $campusMock = $this->createMock(Campus::class);
        $campusCollectionMock = new CampusCollection([$campusMock]);

        $this->campusDataTransformerMock->expects(self::once())
            ->method('write')
            ->with($campusMock)
            ->willReturnSelf();

        $this->campusDataTransformerMock->expects(self::once())
            ->method('readToShare')
            ->willReturn([]);

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusCollection')
            ->with(1, [])
            ->willReturn($campusCollectionMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-campus-collection', $result);
    }
}
