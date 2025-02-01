<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-17 21:39:32
 */

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Repositories\ChainModuleRepository;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractChainRepository::class)]
#[CoversClass(ChainModuleRepository::class)]
class ChainModuleRepositoryTest extends TestCase
{
    private ChainModuleRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainModuleRepository::class)
            ->onlyMethods(['read', 'readFromRepositories', 'write'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testFunctionNamePersistShouldReturnString(): void
    {
        $result = $this->repository->functionNamePersist();

        $this->assertIsString($result);
        $this->assertEquals('persistModule', $result);
    }

    /**
     * @throws ModuleNotFoundException
     * @throws Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnValueObject(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleMock = $this->createMock(Module::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $moduleIdMock)
            ->willReturn($moduleMock);

        $result = $this->repository->find($moduleIdMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws ModuleNotFoundException
     * @throws Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnNull(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $moduleIdMock)
            ->willReturn(null);

        $result = $this->repository->find($moduleIdMock);

        $this->assertNotInstanceOf(Module::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public function testFindShouldReturnException(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $moduleIdMock)
            ->willThrowException(new \Exception());

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('Module not found by id 1');

        $this->repository->find($moduleIdMock);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $moduleId = $this->createMock(ModuleId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('deleteModule', $moduleId);

        $this->repository->deleteModule($moduleId);
    }

    /**
     * @throws Exception
     */
    public function testPersistEmployeeShouldReturnVoid(): void
    {
        $moduleMock = $this->createMock(Module::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('persistModule', $moduleMock)
            ->willReturn($moduleMock);

        $result = $this->repository->persistModule($moduleMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     * @throws ModulesNotFoundException
     * @throws \Throwable
     */
    public function testGetAllShouldReturnCollection(): void
    {
        $modulesMock = $this->createMock(Modules::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn($modulesMock);

        $result = $this->repository->getAll();

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($modulesMock, $result);
    }

    /**
     * @throws ModulesNotFoundException
     * @throws \Throwable
     */
    public function testGetAllShouldReturnNull(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn(null);

        $result = $this->repository->getAll();

        $this->assertNotInstanceOf(Modules::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws ModulesNotFoundException
     * @throws \Throwable
     */
    public function testGetAllShouldChangePropertyToFalse(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('canPersist');
        $this->assertTrue($method->isProtected());

        $this->repository->getAll();
        $result = $method->invoke($this->repository);

        $this->assertFalse($result);
    }

    /**
     * @throws ModulesNotFoundException
     * @throws \Throwable
     */
    public function testGetAllShouldReturnException(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willThrowException(new \Exception());

        $this->expectException(ModulesNotFoundException::class);
        $this->expectExceptionMessage('Modules not found');

        $this->repository->getAll();
    }
}
