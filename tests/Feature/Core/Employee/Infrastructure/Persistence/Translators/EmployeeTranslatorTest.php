<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Translators;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee as EmployeeDomain;
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
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EmployeeTranslator::class)]
class EmployeeTranslatorTest extends TestCase
{
    private EmployeeFactoryContract|MockObject $factory;
    private EmployeeTranslator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(EmployeeFactoryContract::class);
        $this->translator = new EmployeeTranslator($this->factory);
    }

    public function tearDown(): void
    {
        unset(
            $this->factory,
            $this->translator,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_setModel_should_return_self(): void
    {
        $model = $this->createMock(Employee::class);

        $result = $this->translator->setModel($model);

        $this->assertInstanceOf(EmployeeTranslator::class, $result);
        $this->assertSame($this->translator, $result);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function test_toDomain_should_return_domain_object(): void
    {
        $dateTime = new \DateTime('2024-05-01 21:37:00');
        $modelMock = $this->createMock(Employee::class);
        $employeeMock = $this->createMock(EmployeeDomain::class);

        $modelMock->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeIdMock);

        $modelMock->expects(self::once())
            ->method('identification')
            ->willReturn('12345');

        $identificationMock =  $this->createMock(EmployeeIdentification::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeIdentification')
            ->with('12345')
            ->willReturn($identificationMock);

        $modelMock->expects(self::once())
            ->method('name')
            ->willReturn('Peter');

        $nameMock = $this->createMock(EmployeeName::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeName')
            ->with('Peter')
            ->willReturn($nameMock);

        $modelMock->expects(self::once())
            ->method('lastname')
            ->willReturn('Smith');

        $lastnameMock = $this->createMock(EmployeeLastname::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeLastname')
            ->with('Smith')
            ->willReturn($lastnameMock);

        $modelMock->expects(self::once())
            ->method('state')
            ->willReturn(1);

        $stateMock = $this->createMock(EmployeeState::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeState')
            ->with(1)
            ->willReturn($stateMock);

        $modelMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($dateTime);

        $createdAtMock = $this->createMock(EmployeeCreatedAt::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeCreatedAt')
            ->with($dateTime)
            ->willReturn($createdAtMock);

        $modelMock->expects(self::once())
            ->method('identificationType')
            ->willReturn('test');

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeIdentificationType')
            ->with('test')
            ->willReturn($identificationTypeMock);
        $employeeMock->expects(self::once())
            ->method('setIdentificationType')
            ->with($identificationTypeMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($dateTime);

        $updatedAtMock = $this->createMock(EmployeeUpdateAt::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeUpdatedAt')
            ->with($dateTime)
            ->willReturn($updatedAtMock);
        $employeeMock->expects(self::once())
            ->method('setUpdatedAt')
            ->with($updatedAtMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('address')
            ->willReturn('test');

        $addressMock = $this->createMock(EmployeeAddress::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeAddress')
            ->with('test')
            ->willReturn($addressMock);
        $employeeMock->expects(self::once())
            ->method('setAddress')
            ->with($addressMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('phone')
            ->willReturn('12345');

        $phoneMock = $this->createMock(EmployeePhone::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeePhone')
            ->with('12345')
            ->willReturn($phoneMock);
        $employeeMock->expects(self::once())
            ->method('setPhone')
            ->with($phoneMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('email')
            ->willReturn('test@some.com');

        $emailMock = $this->createMock(EmployeeEmail::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeEmail')
            ->with('test@some.com')
            ->willReturn($emailMock);
        $employeeMock->expects(self::once())
            ->method('setEmail')
            ->with($emailMock)
            ->willReturnSelf();

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $modelMock->expects(self::once())
            ->method('user')
            ->willReturn($userMock);

        $userIdMock = $this->createMock(EmployeeUserId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeUserId')
            ->with(1)
            ->willReturn($userIdMock);
        $employeeMock->expects(self::once())
            ->method('setUserId')
            ->with($userIdMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('search')
            ->willReturn('testing');

        $searchMock = $this->createMock(EmployeeSearch::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeSearch')
            ->with('testing')
            ->willReturn($searchMock);
        $employeeMock->expects(self::once())
            ->method('setSearch')
            ->with($searchMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($dateTime);

        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeBirthdate')
            ->with($dateTime)
            ->willReturn($birthdateMock);
        $employeeMock->expects(self::once())
            ->method('setBirthdate')
            ->with($birthdateMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('observations')
            ->willReturn('test');

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeObservations')
            ->with('test')
            ->willReturn($observationsMock);
        $employeeMock->expects(self::once())
            ->method('setObservations')
            ->with($observationsMock)
            ->willReturnSelf();

        $modelMock->expects(self::once())
            ->method('image')
            ->willReturn('image.jpg');

        $imageMock = $this->createMock(EmployeeImage::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeImage')
            ->with('image.jpg')
            ->willReturn($imageMock);
        $employeeMock->expects(self::once())
            ->method('setImage')
            ->with($imageMock)
            ->willReturnSelf();

        $this->factory->expects(self::once())
            ->method('buildEmployee')
            ->with(
                $employeeIdMock,
                $identificationMock,
                $nameMock,
                $lastnameMock,
                $stateMock,
                $createdAtMock
            )
            ->willReturn($employeeMock);

        $this->translator->setModel($modelMock);
        $result = $this->translator->toDomain();

        $this->assertInstanceOf(EmployeeDomain::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    public function test_setCollection_should_return_self(): void
    {
        $result = $this->translator->setCollection([1]);

        $this->assertInstanceOf(EmployeeTranslator::class, $result);
        $this->assertSame($result, $this->translator);
    }

    public function test_toDomainCollection_should_return_employees(): void
    {
        $this->translator->setCollection([1]);
        $result = $this->translator->toDomainCollection();

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertIsArray($result->aggregator());
        $this->assertCount(1, $result->aggregator());
        $this->assertSame([1], $result->aggregator());
    }
}
