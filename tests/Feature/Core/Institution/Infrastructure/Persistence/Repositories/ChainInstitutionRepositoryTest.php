<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-17 21:24:30
 */

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Exceptions\InstitutionNotFoundException;
use Core\Institution\Exceptions\InstitutionsNotFoundException;
use Core\Institution\Infrastructure\Persistence\Repositories\ChainInstitutionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainInstitutionRepository::class)]
class ChainInstitutionRepositoryTest extends TestCase
{
    private ChainInstitutionRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainInstitutionRepository::class)
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
        $this->assertEquals('persistInstitution', $result);
    }

    /**
     * @return void
     * @throws Exception|\Throwable
     */
    public function test_find_should_return_value_object(): void
    {
        $institutionIdMock = $this->createMock(InstitutionId::class);
        $institutionMock = $this->createMock(Institution::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $institutionIdMock)
            ->willReturn($institutionMock);

        $result = $this->repository->find($institutionIdMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @return void
     * @throws Exception|\Throwable
     */
    public function test_find_should_return_null(): void
    {
        $institutionIdMock = $this->createMock(InstitutionId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $institutionIdMock)
            ->willReturn(null);

        $result = $this->repository->find($institutionIdMock);

        $this->assertNotInstanceOf(Institution::class, $result);
        $this->assertNull($result);
    }

    /**
     * @return void
     * @throws Exception|\Throwable
     */
    public function test_find_should_return_Exception(): void
    {
        $institutionIdMock = $this->createMock(InstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $institutionIdMock)
            ->willThrowException(new \Exception);

        $this->expectException(InstitutionNotFoundException::class);
        $this->expectExceptionMessage('Institution not found by id 1');

        $this->repository->find($institutionIdMock);
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Throwable
     */
    public function test_getAll_should_return_collection(): void
    {
        $institutionsMock = $this->createMock(Institutions::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn($institutionsMock);

        $result = $this->repository->getAll();

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($institutionsMock, $result);
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Throwable
     */
    public function test_getAll_should_return_null(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn(null);

        $result = $this->repository->getAll();

        $this->assertNotInstanceOf(Institutions::class, $result);
        $this->assertNull($result);
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Throwable
     */
    public function test_getAll_should_return_Exception(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willThrowException(new \Exception);

        $this->expectException(InstitutionsNotFoundException::class);
        $this->expectExceptionMessage('Institutions not found');

        $this->repository->getAll();
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Exception
     */
    public function test_delete_should_return_void(): void
    {
        $institutionId = $this->createMock(InstitutionId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('delete', $institutionId);

        $this->repository->delete($institutionId);
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Exception
     */
    public function test_persistEmployee_should_return_void(): void
    {
        $institutionMock = $this->createMock(Institution::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('persistInstitution', $institutionMock)
            ->willReturn($institutionMock);

        $result = $this->repository->persistInstitution($institutionMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }
}
