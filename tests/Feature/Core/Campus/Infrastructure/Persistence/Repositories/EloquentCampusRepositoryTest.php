<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\ValueObjects\CampusAddress;
use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use Core\Campus\Domain\ValueObjects\CampusEmail;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Domain\ValueObjects\CampusName;
use Core\Campus\Domain\ValueObjects\CampusObservations;
use Core\Campus\Domain\ValueObjects\CampusPhone;
use Core\Campus\Domain\ValueObjects\CampusSearch;
use Core\Campus\Domain\ValueObjects\CampusState;
use Core\Campus\Exceptions\CampusCollectionNotFoundException;
use Core\Campus\Exceptions\CampusNotFoundException;
use Core\Campus\Infrastructure\Persistence\Eloquent\Model\Campus as CampusModel;
use Core\Campus\Infrastructure\Persistence\Repositories\EloquentCampusRepository;
use Core\Campus\Infrastructure\Persistence\Translators\CampusTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EloquentCampusRepository::class)]
class EloquentCampusRepositoryTest extends TestCase
{
    private CampusModel|MockObject $campusModel;
    private CampusTranslator|MockObject $campusTranslator;
    private DatabaseManager|MockInterface $databaseManager;
    private EloquentCampusRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusModel = $this->createMock(CampusModel::class);
        $this->campusTranslator = $this->createMock(CampusTranslator::class);
        $this->databaseManager = $this->mock(DatabaseManager::class);
        $this->repository = new EloquentCampusRepository(
            $this->databaseManager,
            $this->campusTranslator,
            $this->campusModel
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->campusModel,
            $this->campusTranslator,
            $this->databaseManager,
            $this->repository
        );
        parent::tearDown();
    }

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(50, $result);
    }

    public function testChangePriorityShouldChangeAndReturnSelf(): void
    {
        $result = $this->repository->changePriority(100);

        $this->assertInstanceOf(EloquentCampusRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(100, $result->priority());
    }

    /**
     * @throws CampusNotFoundException
     * @throws Exception
     */
    public function testFindShouldReturnObject(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->campusModel->expects(self::once())
            ->method('getTable')
            ->willReturn('campus');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_state', '>', -1)
            ->andReturnSelf();

        $modelMock = new \stdClass();
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('campus')
            ->andReturn($builderMock);

        $this->campusModel->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->campusTranslator->expects(self::once())
            ->method('setModel')
            ->with($this->campusModel)
            ->willReturnSelf();

        $campusMock = $this->createMock(Campus::class);
        $this->campusTranslator->expects(self::once())
            ->method('toDomain')
            ->willReturn($campusMock);

        $result = $this->repository->find($campusIdMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($result, $campusMock);
    }

    /**
     * @throws Exception
     * @throws CampusNotFoundException
     */
    public function testFindShouldReturnException(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $this->campusModel->expects(self::once())
            ->method('getTable')
            ->willReturn('campus');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('campus')
            ->andReturn($builderMock);

        $this->expectException(CampusNotFoundException::class);
        $this->expectExceptionMessage('Campus not found with id 1');

        $this->repository->find($campusIdMock);
    }

    /**
     * @throws Exception
     * @throws CampusCollectionNotFoundException
     */
    public function testGetAllShouldReturnCampusObject(): void
    {
        $campusInstitutionIdMock = $this->createMock(CampusInstitutionId::class);
        $campusInstitutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->campusModel->expects(self::once())
            ->method('getTable')
            ->willReturn('campus');

        $this->campusModel->expects(self::once())
            ->method('getSearchField')
            ->willReturn('cam_search');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam__inst_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('cam_search', 'test')
            ->andReturnSelf();

        $modelMock = $this->createMock(CampusModel::class);
        $builderMock->shouldReceive('get')
            ->once()
            ->with(['cam_id'])
            ->andReturn([$modelMock]);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('campus')
            ->andReturn($builderMock);

        $this->campusModel->expects(self::once())
            ->method('fill')
            ->with((array) $modelMock)
            ->willReturnSelf();

        $this->campusModel->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $this->campusTranslator->expects(self::once())
            ->method('setCollection')
            ->with([1])
            ->willReturnSelf();

        $campusCollection = $this->createMock(CampusCollection::class);
        $campusCollection->expects(self::once())
            ->method('setFilters')
            ->with(['q' => 'test'])
            ->willReturnSelf();

        $this->campusTranslator->expects(self::once())
            ->method('toDomainCollection')
            ->willReturn($campusCollection);

        $result = $this->repository->getAll($campusInstitutionIdMock, ['q' => 'test']);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($result, $campusCollection);
    }

    /**
     * @throws Exception
     * @throws CampusCollectionNotFoundException
     */
    public function testGetAllShouldReturnException(): void
    {
        $campusInstitutionIdMock = $this->createMock(CampusInstitutionId::class);
        $campusInstitutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->campusModel->expects(self::once())
            ->method('getTable')
            ->willReturn('campus');

        $this->campusModel->expects(self::once())
            ->method('getSearchField')
            ->willReturn('cam_search');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam__inst_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('cam_search', 'test')
            ->andReturnSelf();

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['cam_id'])
            ->andReturn([]);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('campus')
            ->andReturn($builderMock);

        $this->campusModel->expects(self::never())
            ->method('fill');

        $this->campusTranslator->expects(self::never())
            ->method('setCollection');

        $this->campusTranslator->expects(self::never())
            ->method('toDomainCollection');

        $this->expectException(CampusCollectionNotFoundException::class);
        $this->expectExceptionMessage('Campus collection not found');

        $this->repository->getAll($campusInstitutionIdMock, ['q' => 'test']);
    }

    /**
     * @throws CampusNotFoundException
     * @throws Exception
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->campusModel->expects(self::once())
            ->method('getTable')
            ->willReturn('campus');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('campus')
            ->andReturn($builderMock);

        $this->campusModel->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->with([])
            ->andReturn(1);

        $this->campusModel->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $this->repository->delete($campusIdMock);
        $this->assertTrue(true);
    }

    /**
     * @throws CampusNotFoundException
     * @throws Exception
     */
    public function testDeleteShouldReturnException(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $this->campusModel->expects(self::once())
            ->method('getTable')
            ->willReturn('campus');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('campus')
            ->andReturn($builderMock);

        $this->campusModel->expects(self::never())
            ->method('fill');

        $builderMock->shouldReceive('update')
            ->never();

        $this->campusModel->expects(self::never())
            ->method('toArray');

        $this->expectException(CampusNotFoundException::class);
        $this->expectExceptionMessage('Campus not found with id 1');

        $this->repository->delete($campusIdMock);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testPersistCampusShouldReturnCampusObject(): void
    {
        $campusMock = $this->createMock(Campus::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);
        $campusMock->expects(self::exactly(3))
            ->method('id')
            ->willReturn($campusIdMock);
        $this->campusModel->expects(self::once())
            ->method('changeId')
            ->with(null)
            ->willReturnSelf();
        $this->campusModel->expects(self::once())
            ->method('id')
            ->willReturn(null);

        $institutionMock = $this->createMock(CampusInstitutionId::class);
        $institutionMock->expects(self::once())
            ->method('value')
            ->willReturn(12345);
        $campusMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionMock);
        $this->campusModel->expects(self::once())
            ->method('changeInstitutionId')
            ->with(12345)
            ->willReturnSelf();

        $nameMock = $this->createMock(CampusName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $campusMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->campusModel->expects(self::once())
            ->method('changeName')
            ->with('name')
            ->willReturnSelf();

        $phoneMock = $this->createMock(CampusPhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn('123456789');
        $campusMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);
        $this->campusModel->expects(self::once())
            ->method('changePhone')
            ->with('123456789')
        ->willReturnSelf();

        $emailMock = $this->createMock(CampusEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn('email');
        $campusMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);
        $this->campusModel->expects(self::once())
            ->method('changeEmail')
            ->with('email')
            ->willReturnSelf();

        $addressMock = $this->createMock(CampusAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn('address');
        $campusMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);
        $this->campusModel->expects(self::once())
            ->method('changeAddress')
            ->with('address')
            ->willReturnSelf();

        $observationsMock = $this->createMock(CampusObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn('observations');
        $campusMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);
        $this->campusModel->expects(self::once())
            ->method('changeObservations')
            ->with('observations')
            ->willReturnSelf();

        $searchMock = $this->createMock(CampusSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn('testing');
        $campusMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);
        $this->campusModel->expects(self::once())
            ->method('changeSearch')
            ->with('testing')
            ->willReturnSelf();

        $stateMock = $this->createMock(CampusState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $campusMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->campusModel->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $createAt = new \DateTime();
        $createAtMock = $this->createMock(CampusCreatedAt::class);
        $createAtMock->expects(self::once())
            ->method('value')
            ->willReturn($createAt);
        $createAtMock->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $campusMock->expects(self::exactly(2))
            ->method('createdAt')
            ->willReturn($createAtMock);
        $this->campusModel->expects(self::once())
            ->method('changeCreatedAt')
            ->with($createAt)
            ->willReturnSelf();

        $this->campusModel->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('cam_id', null)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $builderMock->shouldReceive('insertGetId')
            ->once()
            ->withAnyArgs()
            ->andReturn(1);

        $this->campusModel->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('campus');

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('campus')
            ->andReturn($builderMock);

        $result = $this->repository->persistCampus($campusMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testPersistCampusShouldUpdateReturnCampusObject(): void
    {
        $campusMock = $this->createMock(Campus::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $campusIdMock->expects(self::never())
            ->method('setValue');
        $campusMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($campusIdMock);
        $this->campusModel->expects(self::once())
            ->method('changeId')
            ->with(1)
            ->willReturnSelf();
        $this->campusModel->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $institutionMock = $this->createMock(CampusInstitutionId::class);
        $institutionMock->expects(self::once())
            ->method('value')
            ->willReturn(12345);
        $campusMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionMock);
        $this->campusModel->expects(self::once())
            ->method('changeInstitutionId')
            ->with(12345)
            ->willReturnSelf();

        $nameMock = $this->createMock(CampusName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $campusMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->campusModel->expects(self::once())
            ->method('changeName')
            ->with('name')
            ->willReturnSelf();

        $phoneMock = $this->createMock(CampusPhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn('123456789');
        $campusMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);
        $this->campusModel->expects(self::once())
            ->method('changePhone')
            ->with('123456789')
            ->willReturnSelf();

        $emailMock = $this->createMock(CampusEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn('email');
        $campusMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);
        $this->campusModel->expects(self::once())
            ->method('changeEmail')
            ->with('email')
            ->willReturnSelf();

        $addressMock = $this->createMock(CampusAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn('address');
        $campusMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);
        $this->campusModel->expects(self::once())
            ->method('changeAddress')
            ->with('address')
            ->willReturnSelf();

        $observationsMock = $this->createMock(CampusObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn('observations');
        $campusMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);
        $this->campusModel->expects(self::once())
            ->method('changeObservations')
            ->with('observations')
            ->willReturnSelf();

        $searchMock = $this->createMock(CampusSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn('testing');
        $campusMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);
        $this->campusModel->expects(self::once())
            ->method('changeSearch')
            ->with('testing')
            ->willReturnSelf();

        $stateMock = $this->createMock(CampusState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $campusMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->campusModel->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $createAt = new \DateTime();
        $createAtMock = $this->createMock(CampusCreatedAt::class);
        $createAtMock->expects(self::once())
            ->method('value')
            ->willReturn($createAt);
        $campusMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createAtMock);
        $this->campusModel->expects(self::once())
            ->method('changeCreatedAt')
            ->with($createAt)
            ->willReturnSelf();

        $this->campusModel->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->twice()
            ->with('cam_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $builderMock->shouldReceive('insertGetId')
            ->never();

        $builderMock->shouldReceive('update')
            ->withAnyArgs()
            ->andReturn(1);

        $this->campusModel->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('campus');

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('campus')
            ->andReturn($builderMock);

        $result = $this->repository->persistCampus($campusMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }
}
