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

    public function testIdShouldReturnValueObject(): void
    {
        $result = $this->employee->id();
        $this->assertInstanceOf(EmployeeId::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetIdShouldChangeValueAndReturnSelf(): void
    {
        $id = $this->createMock(EmployeeId::class);
        $result = $this->employee->setId($id);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($id, $this->employee->id());
    }

    public function testIdentificationShouldReturnValueObject(): void
    {
        $result = $this->employee->identification();
        $this->assertInstanceOf(EmployeeIdentification::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetIdentificationShouldChangeAndReturnSelf(): void
    {
        $identification = $this->createMock(EmployeeIdentification::class);
        $result = $this->employee->setIdentification($identification);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($identification, $this->employee->identification());
    }

    public function testNameShouldReturnValueObject(): void
    {
        $result = $this->employee->name();
        $this->assertInstanceOf(EmployeeName::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetNameShouldChangeAndReturnSelf(): void
    {
        $name = $this->createMock(EmployeeName::class);
        $result = $this->employee->setName($name);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($name, $this->employee->name());
    }

    public function testLastnameShouldReturnValueObject(): void
    {
        $result = $this->employee->lastname();
        $this->assertInstanceOf(EmployeeLastname::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetLastnameShouldChangeAndReturnSelf(): void
    {
        $lastname = $this->createMock(EmployeeLastname::class);
        $result = $this->employee->setLastname($lastname);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($lastname, $this->employee->lastname());
    }

    public function testPhoneShouldReturnValueObject(): void
    {
        $result = $this->employee->phone();
        $this->assertInstanceOf(EmployeePhone::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetPhoneShouldChangeAndReturnSelf(): void
    {
        $phone = $this->createMock(EmployeePhone::class);
        $result = $this->employee->setPhone($phone);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($phone, $this->employee->phone());
    }

    public function testEmailShouldReturnValueObject(): void
    {
        $result = $this->employee->email();
        $this->assertInstanceOf(EmployeeEmail::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetEmailShouldChangeAndReturnSelf(): void
    {
        $email = $this->createMock(EmployeeEmail::class);
        $result = $this->employee->setEmail($email);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($email, $this->employee->email());
    }

    public function testAddressShouldReturnValueObject(): void
    {
        $result = $this->employee->address();
        $this->assertInstanceOf(EmployeeAddress::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetAddressShouldChangeAndReturnSelf(): void
    {
        $address = $this->createMock(EmployeeAddress::class);
        $result = $this->employee->setAddress($address);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($address, $this->employee->address());
    }

    public function testStateShouldReturnValueObject(): void
    {
        $result = $this->employee->state();
        $this->assertInstanceOf(EmployeeState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetStateShouldChangeAndReturnSelf(): void
    {
        $state = $this->createMock(EmployeeState::class);
        $result = $this->employee->setState($state);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($state, $this->employee->state());
    }

    public function testCreatedAtShouldReturnValueObject(): void
    {
        $result = $this->employee->createdAt();
        $this->assertInstanceOf(EmployeeCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetCreatedAtShouldChangeAndReturnSelf(): void
    {
        $createdAt = $this->createMock(EmployeeCreatedAt::class);
        $result = $this->employee->setCreatedAt($createdAt);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($createdAt, $this->employee->createdAt());
    }

    public function testUpdateAtShouldReturnValueObject(): void
    {
        $result = $this->employee->updatedAt();
        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetUpdatedAtShouldChangeAndReturnSelf(): void
    {
        $updatedAt = $this->createMock(EmployeeUpdatedAt::class);
        $result = $this->employee->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($updatedAt, $this->employee->updatedAt());
    }

    public function testUserIdShouldReturnValueObject(): void
    {
        $result = $this->employee->userId();
        $this->assertInstanceOf(EmployeeUserId::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetUserIdShouldChangeAndReturnSelf(): void
    {
        $userId = $this->createMock(EmployeeUserId::class);
        $result = $this->employee->setUserId($userId);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($userId, $this->employee->userId());
    }

    public function testSearchShouldReturnValueObject(): void
    {
        $result = $this->employee->search();
        $this->assertInstanceOf(EmployeeSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetSearchShouldChangeAndReturnSelf(): void
    {
        $search = $this->createMock(EmployeeSearch::class);
        $result = $this->employee->setSearch($search);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($search, $this->employee->search());
    }

    public function testBirthdateShouldReturnValueObject(): void
    {
        $result = $this->employee->birthdate();
        $this->assertInstanceOf(EmployeeBirthdate::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetBirthdateShouldChangeAndReturnSelf(): void
    {
        $birthdate = $this->createMock(EmployeeBirthdate::class);
        $result = $this->employee->setBirthdate($birthdate);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($birthdate, $this->employee->birthdate());
    }

    public function testIdentificationTypeShouldReturnValueObject(): void
    {
        $result = $this->employee->identificationType();
        $this->assertInstanceOf(EmployeeIdentificationType::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetIdentificationTypeShouldChangeAndReturnSelf(): void
    {
        $type = $this->createMock(EmployeeIdentificationType::class);
        $result = $this->employee->setIdentificationType($type);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($type, $this->employee->identificationType());
    }

    public function testImageShouldReturnValueObject(): void
    {
        $result = $this->employee->image();
        $this->assertInstanceOf(EmployeeImage::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetImageShouldChangeAndReturnSelf(): void
    {
        $image = $this->createMock(EmployeeImage::class);
        $result = $this->employee->setImage($image);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($image, $this->employee->image());
    }

    public function testObservationsShouldReturnValueObject(): void
    {
        $result = $this->employee->observations();
        $this->assertInstanceOf(EmployeeObservations::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetObservationsShouldChangeAndReturnSelf(): void
    {
        $observations = $this->createMock(EmployeeObservations::class);
        $result = $this->employee->setObservations($observations);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
        $this->assertSame($observations, $this->employee->observations());
    }

    public function testInstitutionIdShouldReturnValueObject(): void
    {
        $result = $this->employee->institutionId();
        $this->assertInstanceOf(EmployeeInstitutionId::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetInstitutionIdShouldChangeAndReturnSelf(): void
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
    public function testRefreshSearchShouldChangeAndReturnSelf(): void
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
