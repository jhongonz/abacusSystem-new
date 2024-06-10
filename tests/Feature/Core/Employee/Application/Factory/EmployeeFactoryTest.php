<?php

namespace Tests\Feature\Core\Employee\Application\Factory;

use Core\Employee\Application\Factory\EmployeeFactory;
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
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdateAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\Employee\Application\Factory\DataProvider\DataProviderEmployeeFactory;
use Tests\TestCase;

#[CoversClass(EmployeeFactory::class)]
class EmployeeFactoryTest extends TestCase
{
    private EmployeeFactory $factory;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employee = $this->createMock(Employees::class);
        $this->factory = new EmployeeFactory;
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderEmployeeFactory::class, 'provider')]
    public function test_buildEmployeeFromArray_should_return_employee_object(array $dataObject): void
    {
        $result = $this->factory->buildEmployeeFromArray($dataObject);

        $data = $dataObject['employee'];
        $this->assertInstanceOf(Employee::class, $result);

        $this->assertSame($data['id'], $result->id()->value());
        $this->assertInstanceOf(EmployeeId::class, $result->id());

        $this->assertSame($data['identification'], $result->identification()->value());
        $this->assertInstanceOf(EmployeeIdentification::class, $result->identification());

        $this->assertSame($data['name'], $result->name()->value());
        $this->assertInstanceOf(EmployeeName::class, $result->name());

        $this->assertSame($data['lastname'], $result->lastname()->value());
        $this->assertInstanceOf(EmployeeLastname::class, $result->lastname());

        $this->assertSame($data['state'], $result->state()->value());
        $this->assertInstanceOf(EmployeeState::class, $result->state());

        $this->assertSame($data['identification_type'], $result->identificationType()->value());
        $this->assertInstanceOf(EmployeeIdentificationType::class, $result->identificationType());

        $this->assertSame($data['userId'], $result->userId()->value());
        $this->assertInstanceOf(EmployeeUserId::class, $result->userId());

        $this->assertSame($data['address'], $result->address()->value());
        $this->assertInstanceOf(EmployeeAddress::class, $result->address());

        $this->assertSame($data['phone'], $result->phone()->value());
        $this->assertInstanceOf(EmployeePhone::class, $result->phone());

        $this->assertSame($data['email'], $result->email()->value());
        $this->assertInstanceOf(EmployeeEmail::class, $result->email());

        $this->assertSame($data['observations'], $result->observations()->value());
        $this->assertInstanceOf(EmployeeObservations::class, $result->observations());

        $this->assertSame($data['image'], $result->image()->value());
        $this->assertInstanceOf(EmployeeImage::class, $result->image());

        $this->assertSame($data['search'], $result->search()->value());
        $this->assertInstanceOf(EmployeeSearch::class, $result->search());

        $this->assertSame($data['birthdate'], $result->birthdate()->value());
        $this->assertInstanceOf(EmployeeBirthdate::class, $result->birthdate());

        $this->assertSame($data['createdAt'], $result->createdAt()->value());
        $this->assertInstanceOf(EmployeeCreatedAt::class, $result->createdAt());

        $this->assertSame($data['updatedAt'], $result->updatedAt()->value());
        $this->assertInstanceOf(EmployeeUpdateAt::class, $result->updatedAt());
    }

    /**
     * @throws Exception
     */
    public function test_buildEmployees_should_return_object(): void
    {
        $employee = $this->createMock(Employee::class);
        $result = $this->factory->buildEmployees($employee);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertCount(1, $result->items());
    }
}
