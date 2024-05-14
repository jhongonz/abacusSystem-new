<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentProfileRepository;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EloquentProfileRepository::class)]
class EloquentProfileRepositoryTest extends TestCase
{
    private ProfileModel|MockObject $model;
    private ProfileTranslator|MockObject $translator;
    private DatabaseManager|MockInterface $databaseManager;
    private EloquentProfileRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = $this->createMock(ProfileModel::class);
        $this->translator = $this->createMock(ProfileTranslator::class);
        $this->databaseManager = $this->mock(DatabaseManager::class);
        $this->repository = new EloquentProfileRepository(
            $this->databaseManager,
            $this->translator,
            $this->model
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

        $this->assertInstanceOf(EloquentProfileRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(100, $result->priority());
    }

    /**
     * @throws Exception
     */
    public function test_persistProfiles_should_return_object(): void
    {
        $profiles = $this->createMock(Profiles::class);
        $result = $this->repository->persistProfiles($profiles);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($profiles, $result);
    }

    /**
     * @throws Exception
     * @throws ProfileNotFoundException
     */
    public function test_find_should_return_object(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_state', '>', -1)
            ->andReturnSelf();

        $modelMock = $this->createMock(ProfileModel::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builderMock);

        $modelMock->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
            ->willReturnSelf();

        $profileMock = $this->createMock(Profile::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($profileMock);

        $result = $this->repository->find($profileId);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     * @throws ProfileNotFoundException
     */
    public function test_find_should_return_exception(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builderMock);

        $this->model->expects(self::never())
            ->method('fill');

        $this->translator->expects(self::never())
            ->method('setModel');

        $this->translator->expects(self::never())
            ->method('toDomain');

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found with id: 1');

        $this->repository->find($profileId);
    }

    /**
     * @throws Exception
     * @throws ProfileNotFoundException
     */
    public function test_findCriteria_should_return_object(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_name', 'test')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_state', '>', -1)
            ->andReturnSelf();

        $modelMock = $this->createMock(ProfileModel::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builderMock);

        $modelMock->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
            ->willReturnSelf();

        $profileMock = $this->createMock(Profile::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($profileMock);

        $result = $this->repository->findCriteria($profileName);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     * @throws ProfileNotFoundException
     */
    public function test_findCriteria_should_return_exception(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::exactly(2))
            ->method('value')
            ->willReturn('test');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_name', 'test')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builderMock);

        $this->model->expects(self::never())
            ->method('fill');

        $this->translator->expects(self::never())
            ->method('setModel');

        $this->translator->expects(self::never())
            ->method('toDomain');

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found with name: test');

        $this->repository->findCriteria($profileName);
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws Exception
     */
    public function test_getAll_should_return_object(): void
    {
        $filters = ['q' => 'test'];

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('pro_search');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('pro_search', 'test')
            ->andReturnSelf();

        $modelMock = $this->createMock(ProfileModel::class);

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['pro_id'])
            ->andReturn([$modelMock]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with((array) $modelMock)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builderMock);

        $this->translator->expects(self::once())
            ->method('setCollection')
            ->with([1])
            ->willReturnSelf();

        $profilesMock = $this->createMock(Profiles::class);
        $profilesMock->expects(self::once())
            ->method('setFilters')
            ->with($filters)
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('toDomainCollection')
            ->willReturn($profilesMock);

        $result = $this->repository->getAll($filters);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($profilesMock, $result);
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws Exception
     */
    public function test_getAll_should_return_exception(): void
    {
        $filters = ['q' => 'test'];

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('pro_search');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('pro_search', 'test')
            ->andReturnSelf();

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['pro_id'])
            ->andReturn(null);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builderMock);

        $this->translator->expects(self::never())
            ->method('setCollection');

        $this->translator->expects(self::never())
            ->method('toDomainCollection');

        $this->expectException(ProfilesNotFoundException::class);
        $this->expectExceptionMessage('Profiles not found');

        $this->repository->getAll($filters);
    }

    /**
     * @throws Exception
     * @throws ProfileNotFoundException
     */
    public function test_deleteProfile_should_return_void(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builder = $this->mock(Builder::class);

        $modelMock = $this->mock(ProfileModel::class);

        $relationMock = $this->mock(BelongsToMany::class);
        $relationMock->shouldReceive('detach')
            ->once()
            ->andReturn(1);

        $modelMock->shouldReceive('pivotModules')
            ->once()
            ->andReturn($relationMock);

        $builder->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $builder->shouldReceive('where')
            ->times(2)
            ->with('pro_id', 1)
            ->andReturnSelf();

        $builder->shouldReceive('delete')
            ->once()
            ->andReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builder);

        $this->repository->deleteProfile($profileId);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws ProfileNotFoundException
     */
    public function test_deleteProfile_should_return_exception(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builder = $this->mock(Builder::class);

        $builder->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $builder->shouldReceive('where')
            ->once()
            ->with('pro_id', 1);

        $builder->shouldReceive('delete')
            ->never();

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('profiles');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('profiles')
            ->andReturn($builder);

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found with id: 1');

        $this->repository->deleteProfile($profileId);
    }

    /**
     * @throws Exception
     */
    public function test_persistProfile_should_return_object(): void
    {
        $profileMock = $this->createMock(Profile::class);

        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);
        $profileIdMock->expects(self::once())
            ->method('setValue')
            ->with(1)
            ->willReturnSelf();
        $profileMock->expects(self::exactly(3))
            ->method('id')
            ->willReturn($profileIdMock);
        $this->model->expects(self::once())
            ->method('changeId')
            ->with(null)
            ->willReturnSelf();

        $nameMock = $this->createMock(ProfileName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $profileMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('test')
            ->willReturnSelf();

        $stateMock = $this->createMock(ProfileState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $searchMock = $this->createMock(ProfileSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $profileMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('test')
            ->willReturnSelf();

        $descriptionMock = $this->createMock(ProfileDescription::class);
        $descriptionMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $profileMock->expects(self::once())
            ->method('description')
            ->willReturn($descriptionMock);
        $this->model->expects(self::once())
            ->method('changeDescription')
            ->with('test')
            ->willReturnSelf();

        $datetime = new \DateTime;
        $createdAt = $this->createMock(ProfileCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $profileMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updatedAt = $this->createMock(ProfileUpdatedAt::class);
        $updatedAt->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $profileMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updatedAt);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('pro_id', null)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('profiles');

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(null);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock->shouldReceive('insertGetId')
            ->once()
            ->with([])
            ->andReturn(1);

        $profileMock->expects(self::once())
            ->method('modulesAggregator')
            ->willReturn([]);

        $relationMock = $this->mock(BelongsToMany::class);
        $relationMock->shouldReceive('sync')
            ->once()
            ->with([])
            ->andReturn([]);

        $this->model->expects(self::once())
            ->method('pivotModules')
            ->willReturn($relationMock);

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('profiles')
            ->andReturn($builderMock);

        $result = $this->repository->persistProfile($profileMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_persistProfile_should_update_and_return_object(): void
    {
        $profileMock = $this->createMock(Profile::class);

        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $profileIdMock->expects(self::never())
            ->method('setValue');
        $profileMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($profileIdMock);
        $this->model->expects(self::once())
            ->method('changeId')
            ->with(1)
            ->willReturnSelf();

        $nameMock = $this->createMock(ProfileName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $profileMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('test')
            ->willReturnSelf();

        $stateMock = $this->createMock(ProfileState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $searchMock = $this->createMock(ProfileSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $profileMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('test')
            ->willReturnSelf();

        $descriptionMock = $this->createMock(ProfileDescription::class);
        $descriptionMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $profileMock->expects(self::once())
            ->method('description')
            ->willReturn($descriptionMock);
        $this->model->expects(self::once())
            ->method('changeDescription')
            ->with('test')
            ->willReturnSelf();

        $datetime = new \DateTime;
        $createdAt = $this->createMock(ProfileCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $profileMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updatedAt = $this->createMock(ProfileUpdatedAt::class);
        $updatedAt->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $profileMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updatedAt);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('profiles');

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock->shouldReceive('insertGetId')
            ->never();

        $builderMock->shouldReceive('where')
            ->times(2)
            ->with('pro_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->with([])
            ->andReturn(1);

        $profileMock->expects(self::once())
            ->method('modulesAggregator')
            ->willReturn([]);

        $relationMock = $this->mock(BelongsToMany::class);
        $relationMock->shouldReceive('sync')
            ->once()
            ->with([])
            ->andReturn([]);

        $this->model->expects(self::once())
            ->method('pivotModules')
            ->willReturn($relationMock);

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('profiles')
            ->andReturn($builderMock);

        $result = $this->repository->persistProfile($profileMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }
}
