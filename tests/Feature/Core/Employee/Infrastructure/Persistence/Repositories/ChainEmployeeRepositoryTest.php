<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-17 20:22:30
 */

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeesNotFoundException;
use Core\Employee\Infrastructure\Persistence\Repositories\ChainEmployeeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainEmployeeRepository::class)]
class ChainEmployeeRepositoryTest extends TestCase
{
    private ChainEmployeeRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainEmployeeRepository::class)
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
        $this->assertEquals('persistEmployee', $result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindShouldReturnValueObject(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeMock = $this->createMock(Employee::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $employeeIdMock)
            ->willReturn($employeeMock);

        $result = $this->repository->find($employeeIdMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindShouldReturnNull(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $employeeIdMock)
            ->willReturn(null);

        $result = $this->repository->find($employeeIdMock);

        $this->assertNotInstanceOf(Employee::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindShouldReturnException(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $employeeIdMock)
            ->willThrowException(new \Exception());

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found by id 1');

        $this->repository->find($employeeIdMock);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindCriteriaShouldReturnValueObject(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $employeeMock = $this->createMock(Employee::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $identificationMock)
            ->willReturn($employeeMock);

        $result = $this->repository->findCriteria($identificationMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindCriteriaShouldReturnNull(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $identificationMock)
            ->willReturn(null);

        $result = $this->repository->findCriteria($identificationMock);

        $this->assertNotInstanceOf(Employee::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function testFindCriteriaShouldReturnException(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $identificationMock)
            ->willThrowException(new \Exception());

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found by identification 12345');

        $this->repository->findCriteria($identificationMock);
    }

    /**
     * @throws \Exception
     * @throws Exception
     * @throws \Throwable
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('delete', $employeeId);

        $this->repository->delete($employeeId);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testPersistEmployeeShouldReturnVoid(): void
    {
        $employeeMock = $this->createMock(Employee::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('persistEmployee', $employeeMock)
            ->willReturn($employeeMock);

        $result = $this->repository->persistEmployee($employeeMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function testGetAllShouldReturnCollection(): void
    {
        $employeesMock = $this->createMock(Employees::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn($employeesMock);

        $result = $this->repository->getAll();

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($employeesMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function testGetAllShouldReturnNull(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn(null);

        $result = $this->repository->getAll();

        $this->assertNotInstanceOf(Employees::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function testGetAllShouldReturnException(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willThrowException(new \Exception());

        $this->expectException(EmployeesNotFoundException::class);
        $this->expectExceptionMessage('Employees not found');

        $this->repository->getAll();
    }
}
