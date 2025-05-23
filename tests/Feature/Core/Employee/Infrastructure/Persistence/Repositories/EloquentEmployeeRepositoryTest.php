<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use Core\Employee\Domain\ValueObjects\EmployeeCreatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeIdentificationType;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\Employee\Infrastructure\Persistence\Repositories\EloquentEmployeeRepository;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EloquentEmployeeRepository::class)]
class EloquentEmployeeRepositoryTest extends TestCase
{
    private EmployeeModel|MockObject $model;

    private EmployeeTranslator|MockObject $translator;

    private DatabaseManager|MockInterface $databaseManager;

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

        $this->repository = new EloquentEmployeeRepository(
            $this->databaseManager,
            $this->translator,
            $this->model,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->translator,
            $this->databaseManager,
            $this->repository
        );
        parent::tearDown();
    }

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(50, $result);
    }

    public function testChangePriorityShouldChangeAndReturnSelf(): void
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
    public function testFindShouldReturnEmployeeObject(): void
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
            ->with('emp_state', '>', -1)
            ->andReturnSelf();

        $modelMock = new \stdClass();
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
    public function testFindShouldReturnException(): void
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
            ->with('emp_state', '>', -1)
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

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testFindCriteriaShouldReturnEmployeeObject(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_identification', '12345')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_state', '>', -1)
            ->andReturnSelf();

        $modelMock = new \stdClass();
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

        $result = $this->repository->findCriteria($identificationMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     */
    public function testFindCriteriaShouldReturnException(): void
    {
        $identification = $this->createMock(EmployeeIdentification::class);
        $identification->expects(self::exactly(2))
            ->method('value')
            ->willReturn('12345');

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('employees');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_identification', '12345')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_state', '>', -1)
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

        $this->repository->findCriteria($identification);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
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

        $objectMock = new \stdClass();
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($objectMock);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('changeState')
            ->with(-1)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('changeDeletedAt')
            ->withAnyParameters()
            ->willReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->with([])
            ->andReturn(1);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('employees')
            ->andReturn($builderMock);

        $this->repository->delete($employeeIdMock);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws EmployeeNotFoundException
     */
    public function testDeleteShouldReturnException(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
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

        $builderMock->shouldReceive('first')
            ->andReturn(null);

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('employees')
            ->andReturn($builderMock);

        $this->expectException(EmployeeNotFoundException::class);
        $this->expectExceptionMessage('Employee not found with id: 1');

        $this->repository->delete($employeeIdMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testPersistEmployeeShouldReturnEmployeeObject(): void
    {
        $employeeMock = $this->createMock(Employee::class);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);
        $employeeMock->expects(self::exactly(3))
            ->method('id')
            ->willReturn($employeeIdMock);
        $this->model->expects(self::once())
            ->method('changeId')
            ->with(null)
            ->willReturnSelf();

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');
        $employeeMock->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);
        $this->model->expects(self::once())
            ->method('changeIdentification')
            ->with('12345')
            ->willReturnSelf();

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $identificationTypeMock->expects(self::once())
            ->method('value')
            ->willReturn('type');
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($identificationTypeMock);
        $this->model->expects(self::once())
            ->method('changeIdentificationType')
            ->with('type')
            ->willReturnSelf();

        $nameMock = $this->createMock(EmployeeName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $employeeMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('name')
            ->willReturnSelf();

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $lastnameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('lastname')
            ->willReturn($lastnameMock);
        $this->model->expects(self::once())
            ->method('changeLastname')
            ->with('test')
            ->willReturnSelf();

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');
        $employeeMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);
        $this->model->expects(self::once())
            ->method('changePhone')
            ->with('12345')
            ->willReturnSelf();

        $datetime = new \DateTime();
        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdateMock);
        $this->model->expects(self::once())
            ->method('changeBirthdate')
            ->with()
            ->willReturnSelf();

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn('test@local.com');
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);
        $this->model->expects(self::once())
            ->method('changeEmail')
            ->with('test@local.com')
            ->willReturnSelf();

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);
        $this->model->expects(self::once())
            ->method('changeAddress')
            ->with('test')
            ->willReturnSelf();

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);
        $this->model->expects(self::once())
            ->method('changeObservations')
            ->with('test')
            ->willReturnSelf();

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image');
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);
        $this->model->expects(self::once())
            ->method('changeImage')
            ->with('image')
            ->willReturnSelf();

        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::exactly(2))
            ->method('institutionId')
            ->willReturn($institutionIdMock);
        $this->model->expects(self::once())
            ->method('changeInstitutionId')
            ->with(1)
            ->willReturnSelf();

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('test')
            ->willReturnSelf();

        $createAtMock = $this->createMock(EmployeeCreatedAt::class);
        $createAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $createAtMock->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $employeeMock->expects(self::exactly(2))
            ->method('createdAt')
            ->willReturn($createAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updateAtMock = $this->createMock(EmployeeUpdatedAt::class);
        $updateAtMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $employeeMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updateAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(null);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_id', null)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $builderMock->shouldReceive('insertGetId')
            ->once()
            ->withAnyArgs()
            ->andReturn(1);

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('employees');

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('employees')
            ->andReturn($builderMock);

        $result = $this->repository->persistEmployee($employeeMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testPersistEmployeeShouldUpdateReturnEmployeeObject(): void
    {
        $employeeMock = $this->createMock(Employee::class);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($employeeIdMock);
        $this->model->expects(self::once())
            ->method('changeId')
            ->with(1)
            ->willReturnSelf();

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');
        $employeeMock->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);
        $this->model->expects(self::once())
            ->method('changeIdentification')
            ->with('12345')
            ->willReturnSelf();

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $identificationTypeMock->expects(self::once())
            ->method('value')
            ->willReturn('type');
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($identificationTypeMock);
        $this->model->expects(self::once())
            ->method('changeIdentificationType')
            ->with('type')
            ->willReturnSelf();

        $nameMock = $this->createMock(EmployeeName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $employeeMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);
        $this->model->expects(self::once())
            ->method('changeName')
            ->with('name')
            ->willReturnSelf();

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $lastnameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('lastname')
            ->willReturn($lastnameMock);
        $this->model->expects(self::once())
            ->method('changeLastname')
            ->with('test')
            ->willReturnSelf();

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');
        $employeeMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);
        $this->model->expects(self::once())
            ->method('changePhone')
            ->with('12345')
            ->willReturnSelf();

        $datetime = new \DateTime();
        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdateMock);
        $this->model->expects(self::once())
            ->method('changeBirthdate')
            ->with()
            ->willReturnSelf();

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn('test@local.com');
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);
        $this->model->expects(self::once())
            ->method('changeEmail')
            ->with('test@local.com')
            ->willReturnSelf();

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);
        $this->model->expects(self::once())
            ->method('changeAddress')
            ->with('test')
            ->willReturnSelf();

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);
        $this->model->expects(self::once())
            ->method('changeObservations')
            ->with('test')
            ->willReturnSelf();

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image');
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);
        $this->model->expects(self::once())
            ->method('changeImage')
            ->with('image')
            ->willReturnSelf();

        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::exactly(2))
            ->method('institutionId')
            ->willReturn($institutionIdMock);
        $this->model->expects(self::once())
            ->method('changeInstitutionId')
            ->with(1)
            ->willReturnSelf();

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);
        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $employeeMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);
        $this->model->expects(self::once())
            ->method('changeSearch')
            ->with('test')
            ->willReturnSelf();

        $createAtMock = $this->createMock(EmployeeCreatedAt::class);
        $createAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $employeeMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updateAtMock = $this->createMock(EmployeeUpdatedAt::class);
        $updateAtMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);
        $updateAtMock->expects(self::once())
            ->method('setValue')
            ->withAnyParameters()
            ->willReturnSelf();
        $employeeMock->expects(self::exactly(3))
            ->method('updatedAt')
            ->willReturn($updateAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $objectMock = new \stdClass();
        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($objectMock);

        $builderMock->shouldReceive('insertGetId')
            ->never();

        $builderMock->shouldReceive('where')
            ->times(2)
            ->with('emp_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->andReturn(1);

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('employees');

        $this->databaseManager->shouldReceive('table')
            ->times(2)
            ->with('employees')
            ->andReturn($builderMock);

        $result = $this->repository->persistEmployee($employeeMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     */
    public function testGetAllShouldReturnEmployeesObject(): void
    {
        $builderMock = $this->mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->with('emp_state', '>', -1)
            ->andReturnSelf();

        $this->model->expects(self::once())
            ->method('getSearchField')
            ->willReturn('emp_search');

        $builderMock->shouldReceive('whereFullText')
            ->once()
            ->with('emp_search', 'test')
            ->andReturnSelf();

        $modelMock = $this->createMock(EmployeeModel::class);

        $builderMock->shouldReceive('get')
            ->once()
            ->with(['emp_id'])
            ->andReturn([$modelMock]);

        $this->model->expects(self::once())
            ->method('fill')
            ->with((array) $modelMock)
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('id')
            ->willReturn(1);

        $this->translator->expects(self::once())
            ->method('setCollection')
            ->with([1])
            ->willReturnSelf();

        $employeesMock = $this->createMock(Employees::class);
        $employeesMock->expects(self::once())
            ->method('setFilters')
            ->with(['q' => 'test'])
            ->willReturnSelf();

        $this->translator->expects(self::once())
            ->method('toDomainCollection')
            ->willReturn($employeesMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('employees');

        $this->databaseManager->shouldReceive('table')
            ->once()
            ->with('employees')
            ->andReturn($builderMock);

        $result = $this->repository->getAll(['q' => 'test']);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $employeesMock);
    }
}
