<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionSearch;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use Core\Institution\Exceptions\InstitutionNotFoundException;
use Core\Institution\Exceptions\InstitutionsNotFoundException;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution as InstitutionModel;
use Core\Institution\Infrastructure\Persistence\Repositories\EloquentInstitutionRepository;
use Core\Institution\Infrastructure\Persistence\Translators\InstitutionTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EloquentInstitutionRepository::class)]
class EloquentInstitutionRepositoryTest extends TestCase
{
    private InstitutionModel|MockObject $model;
    private InstitutionTranslator|MockObject $translator;
    private DatabaseManager|MockInterface $databaseManager;
    private EloquentInstitutionRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = $this->createMock(InstitutionModel::class);
        $this->translator = $this->createMock(InstitutionTranslator::class);
        $this->databaseManager = $this->mock(DatabaseManager::class);
        $this->repository = new EloquentInstitutionRepository(
            $this->model,
            $this->translator,
            $this->databaseManager
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->translator,
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

        $this->assertInstanceOf(EloquentInstitutionRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(100, $result->priority());
    }

    /**
     * @throws Exception
     * @throws InstitutionNotFoundException
     */
    public function test_find_should_return_object(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('institutions');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('inst_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('inst_state', '>', -1)
            ->andReturnSelf();

        $model = $this->createMock(\stdClass::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $this->model->expects(self::once())
            ->method('fill')
            ->with((array) $model)
            ->willReturnSelf();

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('institutions')
            ->andReturn($builderMock);

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
            ->willReturnSelf();

        $institution = $this->createMock(Institution::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($institution);

        $result = $this->repository->find($idMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institution, $result);
    }

    /**
     * @throws Exception
     * @throws InstitutionNotFoundException
     */
    public function test_find_should_return_exception(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('institutions');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('inst_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('inst_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->model->expects(self::never())
            ->method('fill');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('institutions')
            ->andReturn($builderMock);

        $this->translator->expects(self::never())
            ->method('setModel');

        $this->translator->expects(self::never())
            ->method('toDomain');

        $this->expectException(InstitutionNotFoundException::class);
        $this->expectExceptionMessage('Institution not found with id 1');

        $this->repository->find($idMock);
    }

    /**
     * @throws InstitutionsNotFoundException
     * @throws Exception
     */
    public function test_getAll_should_return_collection(): void
    {
        $filters = ['q' => 'testing'];

        $builderMock = $this->mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->with('inst_state', '>', -1)
            ->andReturnSelf();

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('inst_search');

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('inst_search', 'testing')
            ->andReturnSelf();

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['inst_id'])
            ->andReturn([1]);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('institutions');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('institutions')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([1])
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $this->translator->expects(self::once())
            ->method('setCollection')
            ->with([1])
            ->willReturnSelf();

        $institutions = $this->createMock(Institutions::class);
        $institutions->expects(self::once())
            ->method('setFilters')
            ->with($filters)
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('toDomainCollection')
            ->willReturn($institutions);

        $result = $this->repository->getAll($filters);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($institutions, $result);
    }

    /**
     * @throws InstitutionsNotFoundException
     * @throws Exception
     */
    public function test_getAll_should_return_exception(): void
    {
        $filters = ['q' => 'testing'];

        $builderMock = $this->mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->with('inst_state', '>', -1)
            ->andReturnSelf();

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('inst_search');

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('inst_search', 'testing')
            ->andReturnSelf();

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['inst_id'])
            ->andReturn(null);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('institutions');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('institutions')
            ->andReturn($builderMock);

        $this->model->expects(self::never())
            ->method('fill');

        $this->model->expects(self::never())
            ->method('id');

        $this->translator->expects(self::never())
            ->method('setCollection');

        $this->translator->expects(self::never())
            ->method('toDomainCollection');

        $this->expectException(InstitutionsNotFoundException::class);
        $this->expectExceptionMessage('Institutions not found');

        $this->repository->getAll($filters);
    }

    /**
     * @throws Exception
     * @throws InstitutionNotFoundException
     */
    public function test_delete_should_return_void(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $builder = $this->mock(Builder::class);
        $builder->shouldReceive('where')
            ->once()
            ->with('inst_id', 1)
            ->andReturnSelf();

        $model = $this->createMock(\stdClass::class);
        $builder->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('institutions');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('institutions')
            ->andReturn($builder);

        $this->model->expects(self::once())
            ->method('fill')
            ->with((array) $model)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('changeState')
            ->with(-1)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('changeDeletedAt')
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builder->shouldReceive('update')
            ->once()
            ->with([])
            ->andReturn(1);

        $this->repository->delete($idMock);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws InstitutionNotFoundException
     */
    public function test_delete_should_return_exception(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builder = $this->mock(Builder::class);
        $builder->shouldReceive('where')
            ->once()
            ->with('inst_id', 1)
            ->andReturnSelf();

        $builder->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('institutions');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('institutions')
            ->andReturn($builder);

        $this->model->expects(self::never())
            ->method('fill');

        $this->model->expects(self::never())
            ->method('changeState');

        $this->model->expects(self::never())
            ->method('changeDeletedAt');

        $this->model->expects(self::never())
            ->method('toArray');

        $builder->shouldReceive('update')
            ->never();

        $this->expectException(InstitutionNotFoundException::class);
        $this->expectExceptionMessage('Institution not found with id 1');

        $this->repository->delete($idMock);
    }

    /**
     * @throws Exception
     */
    public function test_persistInstitution_should_save_and_return_object(): void
    {
        $institutionMock = $this->createMock(Institution::class);

        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);
        $idMock->expects(self::once())
            ->method('setValue')
            ->with(1)
            ->willReturnSelf();

        $institutionMock->expects(self::exactly(3))
            ->method('id')
            ->willReturn($idMock);
        $this->model->expects(self::once())
            ->method('changeId')
            ->with(null)
            ->willReturnSelf();
        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(null);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $institutionMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('name')
            ->willReturnSelf();

        $shortnameMock = $this->createMock(InstitutionShortname::class);
        $shortnameMock->expects(self::once())
            ->method('value')
            ->willReturn('shortname');
        $institutionMock->expects(self::once())
            ->method('shortname')
            ->willReturn($shortnameMock);
        $this->model->expects(self::once())
            ->method('changeShortname')
            ->with('shortname')
            ->willReturnSelf();

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn('code');
        $institutionMock->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);
        $this->model->expects(self::once())
            ->method('changeCode')
            ->with('code')
            ->willReturnSelf();

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn('logo');
        $institutionMock->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);
        $this->model->expects(self::once())
            ->method('changeLogo')
            ->with('logo')
            ->willReturnSelf();

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('observations');
        $institutionMock->expects(self::once())
            ->method('observations')
            ->willReturn($observations);
        $this->model->expects(self::once())
            ->method('changeObservations')
            ->with('observations')
            ->willReturnSelf();

        $search = $this->createMock(InstitutionSearch::class);
        $search->expects(self::once())
            ->method('value')
            ->willReturn('testing');
        $institutionMock->expects(self::once())
            ->method('search')
            ->willReturn($search);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('testing')
            ->willReturnSelf();

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $datetime = new \DateTime('2024-05-21 11:11:00');
        $createdAtMock = $this->createMock(InstitutionCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $institutionMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updateAt = $this->createMock(InstitutionUpdatedAt::class);
        $updateAt->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $institutionMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updateAt);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builder = $this->mock(Builder::class);
        $builder->shouldReceive('where')
            ->once()
            ->with('inst_id', null)
            ->andReturnSelf();

        $builder->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $builder->shouldReceive('insertGetId')
            ->once()
            ->with([])
            ->andReturn(1);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('institutions');

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('institutions')
            ->andReturn($builder);

        $result = $this->repository->persistInstitution($institutionMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_persistInstitution_should_update_and_return_object(): void
    {
        $institutionMock = $this->createMock(Institution::class);

        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $idMock->expects(self::never())
            ->method('setValue');

        $institutionMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($idMock);
        $this->model->expects(self::once())
            ->method('changeId')
            ->with(1)
            ->willReturnSelf();
        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $institutionMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('name')
            ->willReturnSelf();

        $shortnameMock = $this->createMock(InstitutionShortname::class);
        $shortnameMock->expects(self::once())
            ->method('value')
            ->willReturn('shortname');
        $institutionMock->expects(self::once())
            ->method('shortname')
            ->willReturn($shortnameMock);
        $this->model->expects(self::once())
            ->method('changeShortname')
            ->with('shortname')
            ->willReturnSelf();

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn('code');
        $institutionMock->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);
        $this->model->expects(self::once())
            ->method('changeCode')
            ->with('code')
            ->willReturnSelf();

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn('logo');
        $institutionMock->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);
        $this->model->expects(self::once())
            ->method('changeLogo')
            ->with('logo')
            ->willReturnSelf();

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('observations');
        $institutionMock->expects(self::once())
            ->method('observations')
            ->willReturn($observations);
        $this->model->expects(self::once())
            ->method('changeObservations')
            ->with('observations')
            ->willReturnSelf();

        $search = $this->createMock(InstitutionSearch::class);
        $search->expects(self::once())
            ->method('value')
            ->willReturn('testing');
        $institutionMock->expects(self::once())
            ->method('search')
            ->willReturn($search);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('testing')
            ->willReturnSelf();

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $datetime = new \DateTime('2024-05-21 11:11:00');
        $createdAtMock = $this->createMock(InstitutionCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $institutionMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updateAt = $this->createMock(InstitutionUpdatedAt::class);
        $updateAt->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $institutionMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updateAt);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builder = $this->mock(Builder::class);
        $builder->shouldReceive('where')
            ->once()
            ->with('inst_id', 1)
            ->andReturnSelf();

        $model = $this->createMock(\stdClass::class);
        $builder->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $builder->shouldReceive('insertGetId')
            ->never();

        $builder->shouldReceive('where')
            ->once()
            ->with('inst_id', 1)
            ->andReturnSelf();

        $builder->shouldReceive('update')
            ->once()
            ->andReturn(1);

        $this->model->expects(self::once())
            ->method('fill')
            ->with((array) $model)
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('institutions');

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('institutions')
            ->andReturn($builder);

        $result = $this->repository->persistInstitution($institutionMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }
}
