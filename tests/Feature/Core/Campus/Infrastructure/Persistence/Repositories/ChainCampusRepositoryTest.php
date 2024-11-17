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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainCampusRepository::class)]
class ChainCampusRepositoryTest extends TestCase
{
    private ChainCampusRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainCampusRepository::class)
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
}
