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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainModuleRepository::class)]
class ChainModuleRepositoryTest extends TestCase
{
    private ChainModuleRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainModuleRepository::class)
            ->onlyMethods(['read', 'readFromRepositories','write'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_functionNamePersist_should_return_string(): void
    {
        $result = $this->repository->functionNamePersist();

        $this->assertIsString($result);
        $this->assertEquals('persistModule', $result);
    }

    /**
     * @return void
     * @throws \Core\Profile\Exceptions\ModuleNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function test_find_should_return_value_object(): void
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
     * @return void
     * @throws \Core\Profile\Exceptions\ModuleNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function test_find_should_return_null(): void
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
    public function test_find_should_return_exception(): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $moduleIdMock)
            ->willThrowException(new \Exception);

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('Module not found by id 1');

        $this->repository->find($moduleIdMock);
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Throwable
     */
    public function test_delete_should_return_void(): void
    {
        $moduleId = $this->createMock(ModuleId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('deleteModule', $moduleId);

        $this->repository->deleteModule($moduleId);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_persistEmployee_should_return_void(): void
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
     * @return void
     * @throws Exception
     * @throws \Core\Profile\Exceptions\ModulesNotFoundException
     * @throws \Throwable
     */
    public function test_getAll_should_return_collection(): void
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
     * @return void
     * @throws \Core\Profile\Exceptions\ModulesNotFoundException
     * @throws \Throwable
     */
    public function test_getAll_should_return_null(): void
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
     * @return void
     * @throws ModulesNotFoundException
     * @throws \Throwable
     */
    public function test_getAll_should_return_exception(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willThrowException(new \Exception);

        $this->expectException(ModulesNotFoundException::class);
        $this->expectExceptionMessage('Modules not found');

        $this->repository->getAll();
    }
}
