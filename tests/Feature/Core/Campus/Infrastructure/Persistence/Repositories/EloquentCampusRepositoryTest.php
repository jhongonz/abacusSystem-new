<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
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

    public function test_priority_should_return_int(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(50, $result);
    }

    public function test_changePriority_should_change_and_return_self(): void
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
    public function test_find_should_return_object(): void
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

        $modelMock = new \stdClass;
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
    public function test_find_should_return_exception(): void
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
    public function test_getAll_should_return_campus_object(): void
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
    public function test_getAll_should_return_exception(): void
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
    public function test_delete_should_return_void(): void
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
    public function test_delete_should_return_exception(): void
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
}
