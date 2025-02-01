<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Management;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampus;
use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampus;
use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampusRequest;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusById;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusByIdRequest;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollection;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollectionRequest;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampus;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampusRequest;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Infrastructure\Management\CampusService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CampusService::class)]
class CampusServiceTest extends TestCase
{
    private CampusFactoryContract|MockObject $campusFactory;
    private SearchCampusById|MockObject $searchCampusById;
    private SearchCampusCollection|MockObject $searchCampusCollection;
    private UpdateCampus|MockObject $updateCampus;
    private CreateCampus|MockObject $createCampus;
    private DeleteCampus|MockObject $deleteCampus;
    private CampusService $service;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusFactory = $this->createMock(CampusFactoryContract::class);
        $this->searchCampusById = $this->createMock(SearchCampusById::class);
        $this->searchCampusCollection = $this->createMock(SearchCampusCollection::class);
        $this->updateCampus = $this->createMock(UpdateCampus::class);
        $this->createCampus = $this->createMock(CreateCampus::class);
        $this->deleteCampus = $this->createMock(DeleteCampus::class);
        $this->service = new CampusService(
            $this->campusFactory,
            $this->searchCampusById,
            $this->searchCampusCollection,
            $this->updateCampus,
            $this->createCampus,
            $this->deleteCampus
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->campusFactory,
            $this->searchCampusById,
            $this->searchCampusCollection,
            $this->updateCampus,
            $this->createCampus,
            $this->deleteCampus,
            $this->service
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchCampusByIdShouldReturnObject(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturn($campusIdMock);

        $request = new SearchCampusByIdRequest($campusIdMock);

        $campusMock = $this->createMock(Campus::class);
        $this->searchCampusById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($campusMock);

        $result = $this->service->searchCampusById(1);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchCampusByIdShouldReturnNull(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturn($campusIdMock);

        $request = new SearchCampusByIdRequest($campusIdMock);
        $this->searchCampusById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->service->searchCampusById(1);

        $this->assertNotInstanceOf(Campus::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testSearchCampusCollectionShouldReturnObject(): void
    {
        $campusInstitutionIdMock = $this->createMock(CampusInstitutionId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusInstitutionId')
            ->with(1)
            ->willReturn($campusInstitutionIdMock);

        $campusCollectionMock = $this->createMock(CampusCollection::class);
        $campusCollectionMock->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $campusMock = $this->createMock(Campus::class);
        $this->searchCampusById->expects(self::once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn($campusMock);

        $campusCollectionMock->expects(self::once())
            ->method('addItem')
            ->with($campusMock)
            ->willReturnSelf();

        $requestMock = new SearchCampusCollectionRequest($campusInstitutionIdMock, []);
        $this->searchCampusCollection->expects(self::once())
            ->method('execute')
            ->with($requestMock)
            ->willReturn($campusCollectionMock);

        $result = $this->service->searchCampusCollection(1, []);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($campusCollectionMock, $result);
        $this->assertIsArray($result->items());
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testUpdateCampusShouldReturnObject(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturn($campusIdMock);

        $campusMock = $this->createMock(Campus::class);
        $requestMock = new UpdateCampusRequest($campusIdMock, []);
        $this->updateCampus->expects(self::once())
            ->method('execute')
            ->with($requestMock)
            ->willReturn($campusMock);

        $result = $this->service->updateCampus(1, []);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testCreateCampusShouldReturnObject(): void
    {
        $campusMock = $this->createMock(Campus::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusFromArray')
            ->with([])
            ->willReturn($campusMock);

        $requestMock = new CreateCampusRequest($campusMock);
        $this->createCampus->expects(self::once())
            ->method('execute')
            ->with($requestMock)
            ->willReturn($campusMock);

        $result = $this->service->createCampus([]);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testDeleteCampusShouldReturnVoid(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturn($campusIdMock);

        $requestMock = new DeleteCampusRequest($campusIdMock);
        $this->deleteCampus->expects(self::once())
            ->method('execute')
            ->with($requestMock)
            ->willReturn(null);

        $this->service->deleteCampus(1);
        $this->assertTrue(true);
    }
}
