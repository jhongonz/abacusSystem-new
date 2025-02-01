<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeePersistException;
use Core\Employee\Infrastructure\Persistence\Repositories\RedisEmployeeRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(RedisEmployeeRepository::class)]
class RedisEmployeeRepositoryTest extends TestCase
{
    private EmployeeFactoryContract|MockObject $factory;

    private EmployeeDataTransformerContract|MockObject $dataTransformer;

    private LoggerInterface|MockObject $logger;

    private RedisEmployeeRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(EmployeeFactoryContract::class);
        $this->dataTransformer = $this->createMock(EmployeeDataTransformerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->repository = new RedisEmployeeRepository(
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

        $this->assertSame(100, $result);
        $this->assertIsInt($result);
    }

    public function testChangePriorityShouldReturnSelf(): void
    {
        $result = $this->repository->changePriority(50);

        $this->assertInstanceOf(RedisEmployeeRepository::class, $result);
        $this->assertSame($result, $this->repository);
        $this->assertSame(50, $result->priority());
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindShouldReturnEmployeeObject(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('employee::1')
            ->andReturn('{"createdAt":{"date":"2024-06-04 12:34:56"},"updatedAt":{"date":"2024-06-04 12:34:56"},"birthdate":{"date":"2024-06-04 12:34:56"}}');

        $employeeMock = $this->createMock(Employee::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeFromArray')
            ->withAnyParameters()
            ->willReturn($employeeMock);

        $result = $this->repository->find($employeeIdMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindShouldReturnNull(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('employee::1')
            ->andReturn(null);

        $this->factory->expects(self::never())
            ->method('buildEmployeeFromArray');

        $result = $this->repository->find($employeeIdMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindShouldReturnException(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('employee::1')
            ->andThrow(EmployeeNotFoundException::class, 'not found');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('not found');

        $this->factory->expects(self::never())
            ->method('buildEmployeeFromArray');

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found by id 1');

        $this->repository->find($employeeIdMock);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindCriteriaShouldReturnEmployeeObject(): void
    {
        $employeeIdentificationMock = $this->createMock(EmployeeIdentification::class);
        $employeeIdentificationMock->expects(self::once())
            ->method('value')
            ->willReturn('test');

        Redis::shouldReceive('get')
            ->once()
            ->with('employee::test')
            ->andReturn('{}');

        $employeeMock = $this->createMock(Employee::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeFromArray')
            ->with([])
            ->willReturn($employeeMock);

        $result = $this->repository->findCriteria($employeeIdentificationMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindCriteriaShouldReturnNull(): void
    {
        $employeeIdentificationMock = $this->createMock(EmployeeIdentification::class);
        $employeeIdentificationMock->expects(self::once())
            ->method('value')
            ->willReturn('test');

        Redis::shouldReceive('get')
            ->once()
            ->with('employee::test')
            ->andReturn(null);

        $this->factory->expects(self::never())
            ->method('buildEmployeeFromArray');

        $result = $this->repository->findCriteria($employeeIdentificationMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindCriteriaShouldReturnException(): void
    {
        $employeeIdentificationMock = $this->createMock(EmployeeIdentification::class);
        $employeeIdentificationMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn('test');

        Redis::shouldReceive('get')
            ->once()
            ->with('employee::test')
            ->andThrow(EmployeeNotFoundException::class, 'not found');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('not found');

        $this->factory->expects(self::never())
            ->method('buildEmployeeFromArray');

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found by identification test');

        $this->repository->findCriteria($employeeIdentificationMock);
    }

    /**
     * @throws Exception
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('delete')
            ->once()
            ->with('employee::1')
            ->andReturnUndefined();

        $this->repository->delete($employeeIdMock);
        $this->assertTrue(true);
    }

    public function testGetAllShouldReturnNull(): void
    {
        $result = $this->repository->getAll();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws EmployeePersistException
     */
    public function testPersistEmployeeShouldReturnObject(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('id')
            ->willReturn($employeeIdMock);

        $employeeMock->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($employeeMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('employee::1', '[]')
            ->andReturnUndefined();

        Redis::shouldReceive('set')
            ->once()
            ->with('employee::test', '[]')
            ->andReturnUndefined();

        $result = $this->repository->persistEmployee($employeeMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws EmployeePersistException
     */
    public function testPersistEmployeeShouldReturnException(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('id')
            ->willReturn($employeeIdMock);

        $employeeMock->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($employeeMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('employee::1', '[]')
            ->andThrow(\Exception::class, 'testing');

        Redis::shouldReceive('set')
            ->never()
            ->with('employee::test', '[]');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(EmployeePersistException::class);
        $this->expectExceptionMessage('It could not persist Employee with key employee::1 in redis');

        $this->repository->persistEmployee($employeeMock);
    }
}
