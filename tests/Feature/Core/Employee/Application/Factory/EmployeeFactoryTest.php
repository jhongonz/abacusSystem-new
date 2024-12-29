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
use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\SharedContext\Model\ValueObjectStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Employee\Application\Factory\DataProvider\DataProviderEmployeeFactory;
use Tests\TestCase;

#[CoversClass(EmployeeFactory::class)]
class EmployeeFactoryTest extends TestCase
{
    private EmployeeFactory|MockObject $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new EmployeeFactory();
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $dataObject
     *
     * @throws \Exception
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderEmployeeFactory::class, 'provider')]
    public function testBuildEmployeeFromArrayShouldReturnEmployeeObject(array $dataObject): void
    {
        $data = $dataObject[Employee::TYPE];
        $this->factory = $this->getMockBuilder(EmployeeFactory::class)
            ->onlyMethods([
                'buildEmployee',
                'buildEmployeeId',
                'buildEmployeeIdentification',
                'buildEmployeeName',
                'buildEmployeeLastname',
                'buildEmployeeState',
            ])
            ->getMock();

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with($data['id'])
            ->willReturn($employeeIdMock);

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeIdentification')
            ->with($data['identification'])
            ->willReturn($identificationMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeName')
            ->with($data['name'])
            ->willReturn($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeLastname')
            ->with($data['lastname'])
            ->willReturn($lastnameMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeState')
            ->with($data['state'])
            ->willReturn($stateMock);

        $employeeMock = $this->createMock(Employee::class);
        $this->factory->expects(self::once())
            ->method('buildEmployee')
            ->with(
                $employeeIdMock,
                $identificationMock,
                $nameMock,
                $lastnameMock,
                $stateMock,
            )
            ->willReturn($employeeMock);

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $identificationTypeMock->expects(self::once())
            ->method('setValue')
            ->with($data['identification_type'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($identificationTypeMock);

        $userIdMock = $this->createMock(EmployeeUserId::class);
        $userIdMock->expects(self::once())
            ->method('setValue')
            ->with($data['userId'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with($data['address'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with($data['phone'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with($data['email'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $createdAtMock = $this->createMock(EmployeeCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($data['createdAt']))
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(EmployeeUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($data['updatedAt']))
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($data['birthdate']))
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdateMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with($data['observations'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('setValue')
            ->with($data['image'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('setValue')
            ->with($data['institutionId'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with($data['search'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $result = $this->factory->buildEmployeeFromArray($dataObject);
        $this->assertEquals($employeeMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testBuildEmployeeShouldReturnObject(): void
    {
        $idMock = $this->createMock(EmployeeId::class);
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $nameMock = $this->createMock(EmployeeName::class);
        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $stateMock = $this->createMock(EmployeeState::class);
        $createdAtMock = $this->createMock(EmployeeCreatedAt::class);

        $result = $this->factory->buildEmployee(
            $idMock,
            $identificationMock,
            $nameMock,
            $lastnameMock,
            $stateMock,
            $createdAtMock,
        );

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertEquals($idMock, $result->id());
        $this->assertEquals($identificationMock, $result->identification());
        $this->assertEquals($nameMock, $result->name());
        $this->assertEquals($lastnameMock, $result->lastname());
        $this->assertEquals($stateMock, $result->state());
        $this->assertEquals($createdAtMock, $result->createdAt());
    }

    public function testBuildEmployeeIdShouldReturnObjectWhenValueIsInt(): void
    {
        $result = $this->factory->buildEmployeeId(1);

        $this->assertInstanceOf(EmployeeId::class, $result);
        $this->assertSame(1, $result->value());
    }

    public function testBuildEmployeeIdShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeId();

        $this->assertInstanceOf(EmployeeId::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeIdentificationShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeIdentification('12345');

        $this->assertInstanceOf(EmployeeIdentification::class, $result);
        $this->assertSame('12345', $result->value());
    }

    public function testBuildEmployeeNameShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeName('John Doe');

        $this->assertInstanceOf(EmployeeName::class, $result);
        $this->assertSame('John Doe', $result->value());
    }

    public function testBuildEmployeeLastnameShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeLastname('John Doe');

        $this->assertInstanceOf(EmployeeLastname::class, $result);
        $this->assertSame('John Doe', $result->value());
    }

    public function testBuildEmployeePhoneShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeePhone('123456789');

        $this->assertInstanceOf(EmployeePhone::class, $result);
        $this->assertSame('123456789', $result->value());
    }

    public function testBuildEmployeePhoneShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeePhone();

        $this->assertInstanceOf(EmployeePhone::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeEmailShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeEmail('john.doe@example.com');

        $this->assertInstanceOf(EmployeeEmail::class, $result);
        $this->assertSame('john.doe@example.com', $result->value());
    }

    public function testBuildEmployeeEmailShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeEmail();

        $this->assertInstanceOf(EmployeeEmail::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeAddressShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeAddress('John Doe');

        $this->assertInstanceOf(EmployeeAddress::class, $result);
        $this->assertSame('John Doe', $result->value());
    }

    public function testBuildEmployeeAddressShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeAddress();

        $this->assertInstanceOf(EmployeeAddress::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws \Exception
     */
    public function testBuildEmployeeStateShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeState(ValueObjectStatus::STATE_ACTIVE);

        $this->assertInstanceOf(EmployeeState::class, $result);
        $this->assertSame(2, $result->value());
    }

    /**
     * @throws \Exception
     */
    public function testBuildEmployeeStateShouldReturnObjectWithoutValue(): void
    {
        $result = $this->factory->buildEmployeeState();

        $this->assertInstanceOf(EmployeeState::class, $result);
        $this->assertSame(1, $result->value());
    }

    public function testBuildEmployeeCreatedAtShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeCreatedAt();

        $this->assertInstanceOf(EmployeeCreatedAt::class, $result);
        $this->assertInstanceOf(\DateTime::class, $result->value());
    }

    public function testBuildEmployeeUpdatedAtShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeUpdatedAt(new \DateTime('now'));

        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
        $this->assertInstanceOf(\DateTime::class, $result->value());
    }

    public function testBuildEmployeeUpdatedAtShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeUpdatedAt();

        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeUserIdShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeUserId(1);

        $this->assertInstanceOf(EmployeeUserId::class, $result);
        $this->assertEquals(1, $result->value());
    }

    public function testBuildEmployeeUserIdShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeUserId();

        $this->assertInstanceOf(EmployeeUserId::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeSearchShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeSearch('testing');

        $this->assertInstanceOf(EmployeeSearch::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function testBuildEmployeeSearchShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeSearch();

        $this->assertInstanceOf(EmployeeSearch::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeBirthdateShouldReturnObject(): void
    {
        $date = new \DateTime('1985-10-25');
        $result = $this->factory->buildEmployeeBirthdate($date);

        $this->assertInstanceOf(EmployeeBirthdate::class, $result);
        $this->assertSame($date, $result->value());
    }

    public function testBuildEmployeeObservationsShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeObservations('testing');

        $this->assertInstanceOf(EmployeeObservations::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function testBuildEmployeeObservationsShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeObservations();

        $this->assertInstanceOf(EmployeeObservations::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeIdentificationType(): void
    {
        $result = $this->factory->buildEmployeeIdentificationType('John Doe');

        $this->assertInstanceOf(EmployeeIdentificationType::class, $result);
        $this->assertSame('John Doe', $result->value());
    }

    public function testBuildEmployeeIdentificationTypeWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeIdentificationType();

        $this->assertInstanceOf(EmployeeIdentificationType::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeBirthdateShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeBirthdate();

        $this->assertInstanceOf(EmployeeBirthdate::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeImageShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeImage('testing');

        $this->assertInstanceOf(EmployeeImage::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function testBuildEmployeeImageShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeImage();

        $this->assertInstanceOf(EmployeeImage::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildEmployeeInstitutionIdShouldReturnObject(): void
    {
        $result = $this->factory->buildEmployeeInstitutionId(10);

        $this->assertInstanceOf(EmployeeInstitutionId::class, $result);
        $this->assertSame(10, $result->value());
    }

    public function testBuildEmployeeInstitutionIdShouldReturnObjectWhenValueIsNull(): void
    {
        $result = $this->factory->buildEmployeeInstitutionId();

        $this->assertInstanceOf(EmployeeInstitutionId::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws Exception
     */
    public function testBuildEmployeesShouldReturnObject(): void
    {
        $employee = $this->createMock(Employee::class);
        $result = $this->factory->buildEmployees($employee);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertCount(1, $result->items());
        $this->assertEquals([$employee], $result->items());
    }
}
