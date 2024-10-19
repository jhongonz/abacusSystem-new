<?php

namespace Tests\Feature\Core\Employee\Domain;

use Core\Employee\Domain\Employee;
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
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Employee::class)]
class EmployeeTest extends TestCase
{
    private EmployeeId|MockObject $employeeId;

    private EmployeeIdentification|MockObject $identification;

    private EmployeeName|MockObject $employeeName;

    private Employee $employee;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->employeeId = $this->createMock(EmployeeId::class);
        $this->identification = $this->createMock(EmployeeIdentification::class);
        $this->employeeName = $this->createMock(EmployeeName::class);
        $this->employee = new Employee(
            $this->employeeId,
            $this->identification,
            $this->employeeName
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->employeeId,
            $this->identification,
            $this->employeeName,
            $this->employee
        );
        parent::tearDown();
    }

    public function test_id_should_return_value_object(): void
    {
        $result = $this->employee->id();
        $this->assertInstanceOf(EmployeeId::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setId_should_change_value_and_return_self(): void
    {
        $id = $this->createMock(EmployeeId::class);
        $result = $this->employee->setId($id);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($id, $this->employee->id());
    }

    public function test_identification_should_return_value_object(): void
    {
        $result = $this->employee->identification();
        $this->assertInstanceOf(EmployeeIdentification::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setIdentification_should_change_and_return_self(): void
    {
        $identification = $this->createMock(EmployeeIdentification::class);
        $result = $this->employee->setIdentification($identification);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($identification, $this->employee->identification());
    }

    public function test_name_should_return_value_object(): void
    {
        $result = $this->employee->name();
        $this->assertInstanceOf(EmployeeName::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setName_should_change_and_return_self(): void
    {
        $name = $this->createMock(EmployeeName::class);
        $result = $this->employee->setName($name);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($name, $this->employee->name());
    }

    public function test_lastname_should_return_value_object(): void
    {
        $result = $this->employee->lastname();
        $this->assertInstanceOf(EmployeeLastname::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setLastname_should_change_and_return_self(): void
    {
        $lastname = $this->createMock(EmployeeLastname::class);
        $result = $this->employee->setLastname($lastname);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($lastname, $this->employee->lastname());
    }

    public function test_phone_should_return_value_object(): void
    {
        $result = $this->employee->phone();
        $this->assertInstanceOf(EmployeePhone::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setPhone_should_change_and_return_self(): void
    {
        $phone = $this->createMock(EmployeePhone::class);
        $result = $this->employee->setPhone($phone);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($phone, $this->employee->phone());
    }

    public function test_email_should_return_value_object(): void
    {
        $result = $this->employee->email();
        $this->assertInstanceOf(EmployeeEmail::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setEmail_should_change_and_return_self(): void
    {
        $email = $this->createMock(EmployeeEmail::class);
        $result = $this->employee->setEmail($email);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($email, $this->employee->email());
    }

    public function test_address_should_return_value_object(): void
    {
        $result = $this->employee->address();
        $this->assertInstanceOf(EmployeeAddress::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setAddress_should_change_and_return_self(): void
    {
        $address = $this->createMock(EmployeeAddress::class);
        $result = $this->employee->setAddress($address);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($address, $this->employee->address());
    }

    public function test_state_should_return_value_object(): void
    {
        $result = $this->employee->state();
        $this->assertInstanceOf(EmployeeState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setState_should_change_and_return_self(): void
    {
        $state = $this->createMock(EmployeeState::class);
        $result = $this->employee->setState($state);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($state, $this->employee->state());
    }

    public function test_createdAt_should_return_value_object(): void
    {
        $result = $this->employee->createdAt();
        $this->assertInstanceOf(EmployeeCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setCreatedAt_should_change_and_return_self(): void
    {
        $createdAt = $this->createMock(EmployeeCreatedAt::class);
        $result = $this->employee->setCreatedAt($createdAt);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($createdAt, $this->employee->createdAt());
    }

    public function test_updateAt_should_return_value_object(): void
    {
        $result = $this->employee->updatedAt();
        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setUpdatedAt_should_change_and_return_self(): void
    {
        $updatedAt = $this->createMock(EmployeeUpdatedAt::class);
        $result = $this->employee->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($updatedAt, $this->employee->updatedAt());
    }

    public function test_userId_should_return_value_object(): void
    {
        $result = $this->employee->userId();
        $this->assertInstanceOf(EmployeeUserId::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setUserId_should_change_and_return_self(): void
    {
        $userId = $this->createMock(EmployeeUserId::class);
        $result = $this->employee->setUserId($userId);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($userId, $this->employee->userId());
    }

    public function test_search_should_return_value_object(): void
    {
        $result = $this->employee->search();
        $this->assertInstanceOf(EmployeeSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setSearch_should_change_and_return_self(): void
    {
        $search = $this->createMock(EmployeeSearch::class);
        $result = $this->employee->setSearch($search);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($search, $this->employee->search());
    }

    public function test_birthdate_should_return_value_object(): void
    {
        $result = $this->employee->birthdate();
        $this->assertInstanceOf(EmployeeBirthdate::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setBirthdate_should_change_and_return_self(): void
    {
        $birthdate = $this->createMock(EmployeeBirthdate::class);
        $result = $this->employee->setBirthdate($birthdate);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($birthdate, $this->employee->birthdate());
    }

    public function test_identificationType_should_return_value_object(): void
    {
        $result = $this->employee->identificationType();
        $this->assertInstanceOf(EmployeeIdentificationType::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setIdentificationType_should_change_and_return_self(): void
    {
        $type = $this->createMock(EmployeeIdentificationType::class);
        $result = $this->employee->setIdentificationType($type);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($type, $this->employee->identificationType());
    }

    public function test_image_should_return_value_object(): void
    {
        $result = $this->employee->image();
        $this->assertInstanceOf(EmployeeImage::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setImage_should_change_and_return_self(): void
    {
        $image = $this->createMock(EmployeeImage::class);
        $result = $this->employee->setImage($image);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($image, $this->employee->image());
    }

    public function test_observations_should_return_value_object(): void
    {
        $result = $this->employee->observations();
        $this->assertInstanceOf(EmployeeObservations::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setObservations_should_change_and_return_self(): void
    {
        $observations = $this->createMock(EmployeeObservations::class);
        $result = $this->employee->setObservations($observations);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($observations, $this->employee->observations());
    }

    public function test_institutionId_should_return_value_object(): void
    {
        $result = $this->employee->institutionId();
        $this->assertInstanceOf(EmployeeInstitutionId::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setInstitutionId_should_change_and_return_self(): void
    {
        $institutionId = $this->createMock(EmployeeInstitutionId::class);
        $result = $this->employee->setInstitutionId($institutionId);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($institutionId, $this->employee->institutionId());
    }

    /**
     * @throws Exception
     */
    public function test_refreshSearch_should_change_and_return_self(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setIdentification($identificationMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setName($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $lastnameMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setLastname($lastnameMock);

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setPhone($phoneMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setEmail($emailMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setAddress($addressMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->employee->setObservations($observationsMock);

        $dataExpected = 'test test test test test test test';
        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected)
            ->willReturnSelf();
        $this->employee->setSearch($searchMock);

        $result = $this->employee->refreshSearch();

        $this->assertInstanceOf(Employee::class, $result);
    }
}
