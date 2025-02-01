<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulePersistException;
use Core\Profile\Infrastructure\Persistence\Repositories\RedisModuleRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(RedisModuleRepository::class)]
class RedisModuleRepositoryTest extends TestCase
{
    private ModuleFactoryContract|MockObject $factory;
    private ModuleDataTransformerContract|MockObject $dataTransformer;
    private LoggerInterface|MockObject $logger;
    private RedisModuleRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(ModuleFactoryContract::class);
        $this->dataTransformer = $this->createMock(ModuleDataTransformerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->repository = new RedisModuleRepository(
            $this->factory,
            $this->dataTransformer,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->factory,
            $this->dataTransformer,
            $this->repository,
            $this->logger
        );
        parent::tearDown();
    }

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(100, $result);
    }

    public function testChangePriorityShouldReturnSelf(): void
    {
        $result = $this->repository->changePriority(75);

        $this->assertInstanceOf(RedisModuleRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(75, $result->priority());
    }

    /**
     * @throws ModuleNotFoundException
     * @throws Exception
     */
    public function testFindShouldReturnObject(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $moduleId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('module::1')
            ->andReturn('{"module":{"createdAt":{"date":"2024-06-16 19:35:39.493232","timezone_type":3,"timezone":"UTC"},"updatedAt":{"date":"2024-06-16 19:35:39.493243","timezone_type":3,"timezone":"UTC"}}}');

        $module = $this->createMock(Module::class);
        $this->factory->expects(self::once())
            ->method('buildModuleFromArray')
            ->withAnyParameters()
            ->willReturn($module);

        $result = $this->repository->find($moduleId);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($module, $result);
    }

    /**
     * @throws ModuleNotFoundException
     * @throws Exception
     */
    public function testFindShouldReturnNull(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $moduleId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('module::1')
            ->andReturn(null);

        $this->factory->expects(self::never())
            ->method('buildModuleFromArray');

        $result = $this->repository->find($moduleId);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws ModuleNotFoundException
     */
    public function testFindShouldReturnException(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $moduleId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('module::1')
            ->andThrow(\Exception::class, 'testing');

        $this->factory->expects(self::never())
            ->method('buildModuleFromArray');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('Module not found by id 1');

        $this->repository->find($moduleId);
    }

    public function testGetAllShouldReturnNull(): void
    {
        $result = $this->repository->getAll();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testDeleteModuleShouldReturnVoid(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $moduleId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('delete')
            ->once()
            ->with('module::1')
            ->andReturnUndefined();

        $this->repository->deleteModule($moduleId);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws ModulePersistException
     */
    public function testPersistModuleShouldReturnObject(): void
    {
        $moduleMock = $this->createMock(Module::class);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($moduleMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('module::1', '[]')
            ->andReturnUndefined();

        $result = $this->repository->persistModule($moduleMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     * @throws ModulePersistException
     */
    public function testPersistModuleShouldReturnException(): void
    {
        $moduleMock = $this->createMock(Module::class);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($moduleMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('module::1', '[]')
            ->andThrow(\Exception::class, 'testing');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(ModulePersistException::class);
        $this->expectExceptionMessage('It could not persist Module with key module::1 in redis');

        $this->repository->persistModule($moduleMock);
    }
}
