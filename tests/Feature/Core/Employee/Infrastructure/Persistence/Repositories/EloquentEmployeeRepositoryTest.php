<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\Employee\Infrastructure\Persistence\Repositories\EloquentEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(EloquentEmployeeRepository::class)]
class EloquentEmployeeRepositoryTest extends TestCase
{
    private EmployeeModel|MockObject $model;
    private EmployeeTranslator|MockObject $translator;
    private DatabaseManager|MockInterface $databaseManager;
    private LoggerInterface|MockObject $logger;
    private EloquentEmployeeRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = $this->createMock(EmployeeModel::class);
        $this->translator = $this->createMock(EmployeeTranslator::class);
        $this->databaseManager = $this->mock(DatabaseManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->repository = new EloquentEmployeeRepository(
            $this->databaseManager,
            $this->translator,
            $this->model,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->translator,
            $this->databaseManager,
            $this->logger,
            $this->repository
        );
        parent::tearDown();
    }

    public function test_priority_should_return_int(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(50, $result);
    }

    public function test_changePriority_should_change_and_return_self(): void
    {
        $result = $this->repository->changePriority(100);

        $this->assertInstanceOf(EloquentEmployeeRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(100, $result->priority());
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function test_find_should_return_employee_object(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $employeeId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_state','>', -1)
            ->andReturnSelf();

        $modelMock = mock(Model::class);
        $modelMock->shouldReceive('toArray')
            ->once()
            ->andReturn([]);

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('employees');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('employees')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
            ->willReturnSelf();

        $employeeMock = $this->createMock(Employee::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($employeeMock);

        $result = $this->repository->find($employeeId);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     */
    public function test_find_should_return_exception(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $employeeId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('employees');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_state','>', -1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('employees')
            ->andReturn($builderMock);

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found with id: 1');

        $this->repository->find($employeeId);
    }
}
