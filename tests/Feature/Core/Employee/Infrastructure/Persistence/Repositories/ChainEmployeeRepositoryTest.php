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
use Throwable;

#[CoversClass(ChainEmployeeRepository::class)]
class ChainEmployeeRepositoryTest extends TestCase
{
    private ChainEmployeeRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainEmployeeRepository::class)
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
        $this->assertEquals('persistEmployee', $result);
    }

    /**
     * @return void
     * @throws Exception|Throwable
     */
    public function test_find_should_return_value_object(): void
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
     * @return void
     * @throws Exception|Throwable
     */
    public function test_find_should_return_null(): void
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
     * @return void
     * @throws Exception|Throwable
     */
    public function test_find_should_return_exception(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $employeeIdMock)
            ->willThrowException(new \Exception);

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found by id 1');

        $this->repository->find($employeeIdMock);
    }

    /**
     * @return void
     * @throws Exception|Throwable
     */
    public function test_findCriteria_should_return_value_object(): void
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
     * @return void
     * @throws Exception|Throwable
     */
    public function test_findCriteria_should_return_null(): void
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
     * @return void
     * @throws Exception|Throwable
     */
    public function test_findCriteria_should_return_exception(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $identificationMock)
            ->willThrowException(new \Exception);

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found by identification 12345');

        $this->repository->findCriteria($identificationMock);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws Exception
     * @throws Throwable
     */
    public function test_delete_should_return_void(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('delete', $employeeId);

        $this->repository->delete($employeeId);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws Exception
     */
    public function test_persistEmployee_should_return_void(): void
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
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function test_getAll_should_return_collection(): void
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
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function test_getAll_should_return_null(): void
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
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function test_getAll_should_return_exception(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willThrowException(new \Exception);

        $this->expectException(EmployeesNotFoundException::class);
        $this->expectExceptionMessage('Employees not found');

        $this->repository->getAll();
    }
}
