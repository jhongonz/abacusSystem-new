<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployee;
use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployeeRequest;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeIdentificationType;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Employee\Application\UseCases\UpdateEmployee\DataProvider\DataProviderUpdateEmployee;
use Tests\TestCase;

#[CoversClass(UpdateEmployee::class)]
class UpdateEmployeeTest extends TestCase
{
    private UpdateEmployeeRequest|MockObject $request;

    private EmployeeRepositoryContract|MockObject $repository;

    private UpdateEmployee $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(UpdateEmployeeRequest::class);
        $this->repository = $this->createMock(EmployeeRepositoryContract::class);
        $this->useCase = new UpdateEmployee($this->repository);
    }

    public function tearDown(): void
    {
        unset(
            $this->useCase,
            $this->request,
            $this->repository
        );
        parent::tearDown();
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderUpdateEmployee::class, 'provider')]
    public function test_execute_should_change_and_return_object(array $dataUpdate): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);

        $this->request->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeIdMock);

        $this->request->expects(self::once())
            ->method('data')
            ->willReturn($dataUpdate);

        $employeeMock = $this->createMock(Employee::class);

        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $identificationMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['identifier'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('identification')
            ->willReturn($identificationMock);

        $documentTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $documentTypeMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['typeDocument'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($documentTypeMock);

        $nameMock = $this->createMock(EmployeeName::class);
        $nameMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['name'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $lastnameMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['lastname'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('lastname')
            ->willReturn($lastnameMock);

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['email'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['phone'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['address'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['observations'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['birthdate'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdateMock);

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['state'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['image'])
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $employeeMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $this->repository->expects(self::once())
            ->method('find')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $this->repository->expects(self::once())
            ->method('persistEmployee')
            ->with($employeeMock)
            ->willReturn($employeeMock);

        $result = $this->useCase->execute($this->request);

        $this->assertInstanceOf(Employee::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $requestMock = $this->createMock(DeleteEmployeeRequest::class);
        $this->useCase->execute($requestMock);
    }
}
