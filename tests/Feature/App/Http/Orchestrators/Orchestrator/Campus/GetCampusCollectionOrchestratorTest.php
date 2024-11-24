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
        $campusMock2 = $this->createMock(Campus::class);
        $campusCollectionMock = new CampusCollection([$campusMock, $campusMock2]);

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusCollection')
            ->with(1, [])
            ->willReturn($campusCollectionMock);

        $this->campusDataTransformerMock->expects(self::exactly(2))
            ->method('write')
            ->withAnyParameters()
            ->willReturnSelf();

        $data1 = ['name' => 'data1'];
        $data2 = ['name' => 'data2'];
        $this->campusDataTransformerMock->expects(self::exactly(2))
            ->method('readToShare')
            ->willReturnOnConsecutiveCalls($data1, $data2);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals([$data1, $data2], $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithCollectionNull(): void
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

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusCollection')
            ->with(1, [])
            ->willReturn(null);

        $this->campusDataTransformerMock->expects(self::never())
            ->method('write');

        $this->campusDataTransformerMock->expects(self::never())
            ->method('readToShare');

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
        $this->assertEquals([], $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-campus-collection', $result);
    }
}
