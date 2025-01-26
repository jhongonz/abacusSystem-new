<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-17 20:22:30
 */

namespace Tests\Feature\Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Exceptions\CampusCollectionNotFoundException;
use Core\Campus\Exceptions\CampusNotFoundException;
use Core\Campus\Infrastructure\Persistence\Repositories\ChainCampusRepository;
use Core\Campus\Infrastructure\Persistence\Repositories\EloquentCampusRepository;
use Core\Campus\Infrastructure\Persistence\Repositories\RedisCampusRepository;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Infrastructure\Persistence\SourceNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractChainRepository::class)]
#[CoversClass(ChainCampusRepository::class)]
class ChainCampusRepositoryTest extends TestCase
{
    private ChainCampusRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainCampusRepository::class)
            ->onlyMethods(['read', 'readFromRepositories', 'write', 'canPersist', 'persistence'])
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
        $this->assertEquals('persistCampus', $result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindShouldReturnValueObject(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusMock = $this->createMock(Campus::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $campusIdMock)
            ->willReturn($campusMock);

        $result = $this->repository->find($campusIdMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindShouldReturnNull(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $campusIdMock)
            ->willReturn(null);

        $result = $this->repository->find($campusIdMock);

        $this->assertNotInstanceOf(Campus::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindShouldReturnException(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $campusIdMock)
            ->willThrowException(new \Exception());

        $this->expectException(CampusNotFoundException::class);
        $this->expectExceptionMessage('Campus not found by id 1');

        $this->repository->find($campusIdMock);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     * @throws CampusCollectionNotFoundException
     */
    public function testGetAllShouldReturnCollection(): void
    {
        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $campusCollection = $this->createMock(CampusCollection::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', $institutionIdMock)
            ->willReturn($campusCollection);

        $result = $this->repository->getAll($institutionIdMock);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($campusCollection, $result);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     * @throws CampusCollectionNotFoundException
     */
    public function testGetAllShouldReturnNull(): void
    {
        $institutionIdMock = $this->createMock(CampusInstitutionId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', $institutionIdMock)
            ->willReturn(null);

        $result = $this->repository->getAll($institutionIdMock);

        $this->assertNotInstanceOf(CampusCollection::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     * @throws CampusCollectionNotFoundException
     */
    public function testGetAllShouldReturnException(): void
    {
        $institutionIdMock = $this->createMock(CampusInstitutionId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', $institutionIdMock)
            ->willThrowException(new \Exception());

        $this->expectException(CampusCollectionNotFoundException::class);
        $this->expectExceptionMessage('Campus collection not found');

        $this->repository->getAll($institutionIdMock);
    }

    /**
     * @throws CampusCollectionNotFoundException
     * @throws Exception
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function testGetAllShouldChangePropertyToFalse(): void
    {
        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $this->repository->getAll($institutionIdMock);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('canPersist');
        $this->assertTrue($method->isProtected());

        $result = $method->invoke($this->repository, []);
        $this->assertFalse($result);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $campusId = $this->createMock(CampusId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('delete', $campusId);

        $this->repository->delete($campusId);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testPersistCampusShouldReturnVoid(): void
    {
        $campusMock = $this->createMock(Campus::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('persistCampus', $campusMock)
            ->willReturn($campusMock);

        $result = $this->repository->persistCampus($campusMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testAddRepositoryShouldReturnSelfWhenPriorityAreEquals(): void
    {
        $chainMock1 = $this->createMock(ChainPriority::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(1);

        $chainMock2 = $this->createMock(ChainPriority::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(2);

        $chainMock3 = $this->createMock(ChainPriority::class);
        $chainMock3->expects(self::any())
            ->method('priority')
            ->willReturn(2);

        $result = $this->repository->addRepository($chainMock1, $chainMock2, $chainMock3);

        $this->assertInstanceOf(AbstractChainRepository::class, $result);
        $this->assertSame($this->repository, $result);

        $reflection = new \ReflectionClass(AbstractChainRepository::class);
        $property = $reflection->getProperty('repositories');
        $this->assertTrue($property->isPrivate());

        $repositories = $property->getValue($result);
        $repositoriesExpected = [$chainMock2, $chainMock3, $chainMock1];

        $this->assertSame($repositoriesExpected, $repositories);
    }

    /**
     * @throws Exception
     */
    public function testAddRepositoryShouldReturnSelfWhenPriorityIsHigher(): void
    {
        $chainMock1 = $this->createMock(ChainPriority::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(1);

        $chainMock2 = $this->createMock(ChainPriority::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(10);

        $chainMock3 = $this->createMock(ChainPriority::class);
        $chainMock3->expects(self::any())
            ->method('priority')
            ->willReturn(20);

        $result = $this->repository->addRepository($chainMock1, $chainMock2, $chainMock3);

        $this->assertInstanceOf(AbstractChainRepository::class, $result);
        $this->assertSame($this->repository, $result);

        $reflection = new \ReflectionClass(AbstractChainRepository::class);
        $property = $reflection->getProperty('repositories');
        $this->assertTrue($property->isPrivate());

        $repositories = $property->getValue($result);
        $repositoriesExpected = [$chainMock3, $chainMock2, $chainMock1];

        $this->assertSame($repositoriesExpected, $repositories);
    }

    /**
     * @throws Exception
     */
    public function testAddRepositoryShouldReturnSelfWhenPriorityIsLower(): void
    {
        $chainMock1 = $this->createMock(ChainPriority::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(10);

        $chainMock2 = $this->createMock(ChainPriority::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(1);

        $chainMock3 = $this->createMock(ChainPriority::class);
        $chainMock3->expects(self::any())
            ->method('priority')
            ->willReturn(5);

        $result = $this->repository->addRepository($chainMock1, $chainMock2, $chainMock3);

        $this->assertInstanceOf(AbstractChainRepository::class, $result);
        $this->assertSame($this->repository, $result);

        $reflection = new \ReflectionClass(AbstractChainRepository::class);
        $property = $reflection->getProperty('repositories');
        $this->assertTrue($property->isPrivate());

        $repositories = $property->getValue($result);
        $repositoriesExpected = [$chainMock1, $chainMock3, $chainMock2];

        $this->assertSame($repositoriesExpected, $repositories);
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testWriteShouldReturnException(): void
    {
        $chainMock1 = $this->createMock(ChainPriority::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(1);
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(2);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('write');
        $this->assertTrue($method->isProtected());

        $this->expectException(\InvalidArgumentException::class);

        $method->invokeArgs($this->repository, ['sandbox']);
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testWriteShouldExecuteCorrectly(): void
    {
        $campus = $this->createMock(Campus::class);

        $chainMock1 = $this->createMock(EloquentCampusRepository::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(1);
        $chainMock1->expects(self::once())
            ->method('persistCampus')
            ->with($campus)
            ->willReturn($campus);
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(2);
        $chainMock2->expects(self::once())
            ->method('persistCampus')
            ->with($campus)
            ->willReturn($campus);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('write');
        $this->assertTrue($method->isProtected());

        $result = $method->invokeArgs($this->repository, ['persistCampus', $campus]);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertEquals($campus, $result);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testReadShouldExecuteCorrectly(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusMock = $this->createMock(Campus::class);

        $this->repository->expects(self::once())
            ->method('readFromRepositories')
            ->with('find', $campusIdMock)
            ->willReturn($campusMock);

        $this->repository->expects(self::once())
            ->method('canPersist')
            ->willReturn(true);

        $this->repository->expects(self::once())
            ->method('persistence')
            ->with('persistCampus',$campusMock);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('read');
        $this->assertTrue($method->isProtected());

        $result = $method->invokeArgs($this->repository, ['find', $campusIdMock]);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testReadFromRepositoriesShouldExecuteCorrectlyWithDataInRedis(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusMock = $this->createMock(Campus::class);

        $chainMock1 = $this->createMock(EloquentCampusRepository::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(5);
        $chainMock1->expects(self::never())
            ->method('find');
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(10);
        $chainMock2->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn($campusMock);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('readFromRepositories');
        $this->assertTrue($method->isProtected());

        $result = $method->invokeArgs($this->repository, ['find', $campusIdMock]);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testReadFromRepositoriesShouldExecuteCorrectlyWithDataInEloquent(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusMock = $this->createMock(Campus::class);

        $chainMock1 = $this->createMock(EloquentCampusRepository::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(5);
        $chainMock1->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn($campusMock);
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(10);
        $chainMock2->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn(null);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('readFromRepositories');
        $this->assertTrue($method->isProtected());

        $result = $method->invokeArgs($this->repository, ['find', $campusIdMock]);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testReadFromRepositoriesShouldExecuteCorrectlyAndReturnException(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);

        $chainMock1 = $this->createMock(EloquentCampusRepository::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(5);
        $chainMock1->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn(null);
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(10);
        $chainMock2->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn(null);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('readFromRepositories');
        $this->assertTrue($method->isProtected());

        $this->expectException(SourceNotFoundException::class);
        $this->expectExceptionMessage('Source not found');

        $method->invokeArgs($this->repository, ['find', $campusIdMock]);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testReadFromRepositoriesShouldExecuteCorrectlyAndReturnExceptionInvalidArgument(): void
    {
        $chainMock1 = $this->createMock(EloquentCampusRepository::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(5);
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(10);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);
        $method = $reflection->getMethod('readFromRepositories');
        $this->assertTrue($method->isProtected());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The function sandbox is not a callable.');

        $method->invokeArgs($this->repository, ['sandbox']);
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    /*public function testPersistenceShouldExecuteCorrectlyAndPriority(): void
    {
        $campusMock = $this->createMock(Campus::class);

        $chainMock1 = $this->createMock(EloquentCampusRepository::class);
        $chainMock1->expects(self::any())
            ->method('priority')
            ->willReturn(5);
        $chainMock1->expects(self::never())
            ->method('persistCampus');
        $this->repository->addRepository($chainMock1);

        $chainMock2 = $this->createMock(RedisCampusRepository::class);
        $chainMock2->expects(self::any())
            ->method('priority')
            ->willReturn(10);
        $chainMock2->expects(self::once())
            ->method('persistCampus')
            ->with($campusMock)
            ->willReturn($campusMock);
        $this->repository->addRepository($chainMock2);

        $reflection = new \ReflectionClass(ChainCampusRepository::class);

        $method = $reflection->getMethod('persistence');
        $this->assertTrue($method->isProtected());

        $method->invokeArgs($this->repository, ['persistCampus', $campusMock]);
    }*/
}
