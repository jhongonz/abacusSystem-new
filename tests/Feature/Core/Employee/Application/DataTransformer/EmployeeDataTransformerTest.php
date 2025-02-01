<?php

namespace Tests\Feature\Core\Employee\Application\DataTransformer;

use Core\Employee\Application\DataTransformer\EmployeeDataTransformer;
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
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Employee\Application\DataProvider\EmployeeDataTransformerDataProvider;
use Tests\TestCase;

#[CoversClass(EmployeeDataTransformer::class)]
class EmployeeDataTransformerTest extends TestCase
{
    private Employee|MockObject $employee;

    private EmployeeDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employee = $this->createMock(Employee::class);
        $this->dataTransformer = new EmployeeDataTransformer();
    }

    public function tearDown(): void
    {
        unset(
            $this->employee,
            $this->dataTransformer
        );
        parent::tearDown();
    }

    public function testWriteShouldChangeAndReturnSelf(): void
    {
        $result = $this->dataTransformer->write($this->employee);
        $this->assertInstanceOf(EmployeeDataTransformer::class, $result);
        $this->assertSame($result, $this->dataTransformer);
    }

    /**
     * @param array<string, array<string, mixed>> $data
     *
     * @throws Exception
     */
    #[DataProviderExternal(EmployeeDataTransformerDataProvider::class, 'provider_read')]
    public function testReadShouldReturnArrayWithData(array $data): void
    {
        $idMock = $this->createMock(EmployeeId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn($data['id']);
        $this->employee->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $userIdMock = $this->createMock(EmployeeUserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['userId']);
        $this->employee->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['institutionId']);
        $this->employee->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn($data['identification']);
        $this->employee->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $identificationTypeMock->expects(self::once())
            ->method('value')
            ->willReturn($data['identification_type']);
        $this->employee->expects(self::once())
            ->method('identificationType')
            ->willReturn($identificationTypeMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn($data['name']);
        $this->employee->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $lastnameMock->expects(self::once())
            ->method('value')
            ->willReturn($data['lastname']);
        $this->employee->expects(self::once())
            ->method('lastname')
            ->willReturn($lastnameMock);

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn($data['phone']);
        $this->employee->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn($data['email']);
        $this->employee->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn($data['address']);
        $this->employee->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['birthdate']);
        $this->employee->expects(self::once())
            ->method('birthdate')
            ->willreturn($birthdateMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn($data['observations']);
        $this->employee->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn($data['image']);
        $this->employee->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn($data['search']);
        $this->employee->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn($data['state']);
        $this->employee->expects(self::once())
            ->method('state')
            ->willreturn($stateMock);

        $createdAtMock = $this->createMock(EmployeeCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['createdAt']);
        $this->employee->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(EmployeeUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['updatedAt']);
        $this->employee->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $this->dataTransformer->write($this->employee);
        $result = $this->dataTransformer->read();
        $dataResult = $result[Employee::TYPE];

        $this->assertIsArray($result);
        $this->assertArrayHasKey(Employee::TYPE, $result);
        $this->assertIsArray($dataResult);
        $this->assertEquals($data, $dataResult);
    }

    /**
     * @param array<string, array<string, mixed>> $data
     *
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(EmployeeDataTransformerDataProvider::class, 'provider_readToShare')]
    public function testReadToShareShouldReturnArrayWithData(array $data): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['id']);
        $this->employee->expects(self::once())
            ->method('id')
            ->willReturn($employeeIdMock);

        $userIdMock = $this->createMock(EmployeeUserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['userId']);
        $this->employee->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['institutionId']);
        $this->employee->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('value')
            ->willReturn($data['identification']);
        $this->employee->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $identificationTypeMock->expects(self::once())
            ->method('value')
            ->willReturn($data['identification_type']);
        $this->employee->expects(self::once())
            ->method('identificationType')
            ->willReturn($identificationTypeMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn($data['name']);
        $this->employee->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $lastnameMock->expects(self::once())
            ->method('value')
            ->willReturn($data['lastname']);
        $this->employee->expects(self::once())
            ->method('lastname')
            ->willReturn($lastnameMock);

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn($data['phone']);
        $this->employee->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn($data['email']);
        $this->employee->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn($data['address']);
        $this->employee->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['birthdate']);
        $this->employee->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdateMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn($data['observations']);
        $this->employee->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn($data['image']);
        $this->employee->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn($data['search']);
        $this->employee->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('formatHtmlToState')
            ->willReturn($data['html']);

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn($data['state']);

        $this->employee->expects(self::exactly(2))
            ->method('state')
            ->willReturn($stateMock);

        $createdAtMock = $this->createMock(EmployeeCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['createdAt']);
        $this->employee->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(EmployeeUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['updatedAt']);
        $this->employee->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $this->dataTransformer->write($this->employee);
        $result = $this->dataTransformer->readToShare();

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey(Employee::TYPE, $result);
        $this->assertArrayHasKey('state_literal', $result);
    }
}
