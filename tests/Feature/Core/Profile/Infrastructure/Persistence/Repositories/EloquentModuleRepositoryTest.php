<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module as ModuleModel;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentModuleRepository;
use Core\Profile\Infrastructure\Persistence\Translators\ModuleTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EloquentModuleRepository::class)]
class EloquentModuleRepositoryTest extends TestCase
{
    private ModuleModel|MockObject $model;
    private ModuleTranslator|MockObject $translator;
    private DatabaseManager|MockInterface $databaseManager;
    private EloquentModuleRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = $this->createMock(ModuleModel::class);
        $this->translator = $this->createMock(ModuleTranslator::class);
        $this->databaseManager = $this->mock(DatabaseManager::class);
        $this->repository = new EloquentModuleRepository(
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

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(50, $result);
    }

    public function testChangePriorityShouldChangeAndReturnSelf(): void
    {
        $result = $this->repository->changePriority(100);

        $this->assertInstanceOf(EloquentModuleRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(100, $result->priority());
    }

    /**
     * @throws Exception
     * @throws ModuleNotFoundException
     */
    public function testFindShouldReturnModuleObject(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $modelMock = new \stdClass();

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('modules');

        $builderMock = $this->mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('modules')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
            ->willReturnSelf();

        $moduleMock = $this->createMock(Module::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($moduleMock);

        $result = $this->repository->find($moduleIdMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     * @throws ModuleNotFoundException
     */
    public function testFindShouldReturnException(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('modules');

        $builderMock = $this->mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('modules')
            ->andReturn($builderMock);

        $this->model->expects(self::never())
            ->method('fill');

        $this->translator->expects(self::never())
            ->method('setModel');

        $this->translator->expects(self::never())
            ->method('toDomain');

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('Module not found with id: 1');

        $this->repository->find($moduleIdMock);
    }

    /**
     * @throws Exception
     */
    public function testPersistModuleShouldReturnObject(): void
    {
        $moduleMock = $this->createMock(Module::class);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);
        $moduleIdMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();
        $moduleMock->expects(self::exactly(3))
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->model->expects(self::once())
            ->method('changeId')
            ->with(null)
            ->willReturnSelf();

        $nameMock = $this->createMock(ModuleName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('test')
            ->willReturnSelf();

        $menuKey = $this->createMock(ModuleMenuKey::class);
        $menuKey->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKey);
        $this->model->expects(self::once())
            ->method('changeMenuKey')
            ->with('test')
            ->willReturnSelf();

        $route = $this->createMock(ModuleRoute::class);
        $route->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('route')
            ->willReturn($route);
        $this->model->expects(self::once())
            ->method('changeRoute')
            ->with('test')
            ->willReturnSelf();

        $icon = $this->createMock(ModuleIcon::class);
        $icon->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('icon')
            ->willReturn($icon);
        $this->model->expects(self::once())
            ->method('changeIcon')
            ->with('test')
            ->willReturnSelf();

        $state = $this->createMock(ModuleState::class);
        $state->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($state);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $search = $this->createMock(ModuleSearch::class);
        $search->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('search')
            ->willReturn($search);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('test')
            ->willReturnSelf();

        $datetime = new \DateTime();
        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $createdAt->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $moduleMock->expects(self::exactly(2))
            ->method('createdAt')
            ->willReturn($createdAt);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $updatedAt->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $moduleMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updatedAt);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('modules');

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(null);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_id', null)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $builderMock->shouldReceive('insertGetId')
            ->once()
            ->withAnyArgs()
            ->andReturn(2);

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('modules')
            ->andReturn($builderMock);

        $result = $this->repository->persistModule($moduleMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testPersistModuleShouldUpdateAndReturnObject(): void
    {
        $moduleMock = $this->createMock(Module::class);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(2);
        $moduleIdMock->expects(self::never())
            ->method('setValue');
        $moduleMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->model->expects(self::once())
            ->method('changeId')
            ->with(2)
            ->willReturnSelf();

        $nameMock = $this->createMock(ModuleName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('test')
            ->willReturnSelf();

        $menuKey = $this->createMock(ModuleMenuKey::class);
        $menuKey->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKey);
        $this->model->expects(self::once())
            ->method('changeMenuKey')
            ->with('test')
            ->willReturnSelf();

        $route = $this->createMock(ModuleRoute::class);
        $route->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('route')
            ->willReturn($route);
        $this->model->expects(self::once())
            ->method('changeRoute')
            ->with('test')
            ->willReturnSelf();

        $icon = $this->createMock(ModuleIcon::class);
        $icon->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('icon')
            ->willReturn($icon);
        $this->model->expects(self::once())
            ->method('changeIcon')
            ->with('test')
            ->willReturnSelf();

        $state = $this->createMock(ModuleState::class);
        $state->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($state);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $search = $this->createMock(ModuleSearch::class);
        $search->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $moduleMock->expects(self::once())
            ->method('search')
            ->willReturn($search);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('test')
            ->willReturnSelf();

        $datetime = new \DateTime();
        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $moduleMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $updatedAt->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $updatedAt->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $moduleMock->expects(self::exactly(3))
            ->method('updatedAt')
            ->willReturn($updatedAt);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('modules');

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(2);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn([]);

        $builderMock->shouldReceive('insertGetId')
            ->never();

        $builderMock->shouldReceive('where')
            ->times(2)
            ->with('mod_id', 2)
            ->andReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->withAnyArgs()
            ->andReturn(2);

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('modules')
            ->andReturn($builderMock);

        $result = $this->repository->persistModule($moduleMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Exception
     */
    public function testGetAllShouldReturnCollection(): void
    {
        $filters = ['q' => 'test'];

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('mod_search', 'test')
            ->andReturnSelf();

        $builderMock->shouldReceive('orderBy')
            ->once()
            ->with('mod_position')
            ->andReturnSelf();

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('modules');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('modules')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('mod_search');

        $modelMock = $this->createMock(ModuleModel::class);

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['mod_id'])
            ->andReturn([$modelMock]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with((array) $modelMock)
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('id')
            ->willReturn(1);

        $this->translator->expects(self::once())
            ->method('setCollection')
            ->with([1])
            ->willReturnSelf();

        $modulesMock = $this->createMock(Modules::class);
        $modulesMock->expects(self::once())
            ->method('setFilters')
            ->with($filters)
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('toDomainCollection')
            ->willReturn($modulesMock);

        $result = $this->repository->getAll($filters);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($modulesMock, $result);
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Exception
     */
    public function testGetAllShouldReturnException(): void
    {
        $filters = ['q' => 'test'];

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_state', '>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('mod_search', 'test')
            ->andReturnSelf();

        $builderMock->shouldReceive('orderBy')
            ->once()
            ->with('mod_position')
            ->andReturnSelf();

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('modules');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('modules')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('mod_search');

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['mod_id'])
            ->andReturn([]);

        $this->translator->expects(self::never())
            ->method('setCollection');

        $this->translator->expects(self::never())
            ->method('toDomainCollection');

        $this->expectException(ModulesNotFoundException::class);
        $this->expectExceptionMessage('Modules not found');

        $this->repository->getAll($filters);
    }

    /**
     * @throws Exception
     * @throws ModuleNotFoundException
     */
    public function testDeleteModuleShouldReturnVoid(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_id', 1)
            ->andReturnSelf();

        $modelMock = $this->createMock(\stdClass::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->model->expects(self::once())
            ->method('fill')
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock->shouldReceive('update')
            ->once()
            ->with([])
            ->andReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('modules');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('modules')
            ->andReturn($builderMock);

        $this->repository->deleteModule($moduleIdMock);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws ModuleNotFoundException
     */
    public function testDeleteModuleShouldReturnException(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('mod_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $builderMock->shouldReceive('delete')
            ->never();

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('modules');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('modules')
            ->andReturn($builderMock);

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('Module not found with id: 1');

        $this->repository->deleteModule($moduleIdMock);
    }
}
