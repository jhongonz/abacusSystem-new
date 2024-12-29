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
use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Employee\Infrastructure\Persistence\Translators\DataProvider\EmployeeTranslatorDataProvider;
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
    public function testSetModelShouldReturnSelf(): void
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
    #[DataProviderExternal(EmployeeTranslatorDataProvider::class, 'provider')]
    public function testToDomainShouldReturnDomainObject(?string $dataUser): void
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

        $identificationMock = $this->createMock(EmployeeIdentification::class);
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
            ->method('identificationType')
            ->willReturn('test');

        $identificationTypeMock = $this->createMock(EmployeeIdentificationType::class);
        $identificationTypeMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('identificationType')
            ->willReturn($identificationTypeMock);

        $modelMock->expects(self::exactly(2))
            ->method('createdAt')
            ->willReturn($dateTime);

        $createdAtMock = $this->createMock(EmployeeCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('setValue')
            ->with($dateTime)
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $modelMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($dateTime);

        $updatedAtMock = $this->createMock(EmployeeUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with($dateTime)
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $modelMock->expects(self::once())
            ->method('address')
            ->willReturn('test');

        $addressMock = $this->createMock(EmployeeAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $modelMock->expects(self::once())
            ->method('phone')
            ->willReturn('12345');

        $phoneMock = $this->createMock(EmployeePhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with('12345')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $modelMock->expects(self::once())
            ->method('email')
            ->willReturn('test@some.com');

        $emailMock = $this->createMock(EmployeeEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with('test@some.com')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $modelMock->expects(self::once())
            ->method('search')
            ->willReturn('testing');

        $searchMock = $this->createMock(EmployeeSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with('testing')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $modelMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($dateTime);

        $birthdateMock = $this->createMock(EmployeeBirthdate::class);
        $birthdateMock->expects(self::once())
            ->method('setValue')
            ->with($dateTime)
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('birthdate')
            ->willReturn($birthdateMock);

        $modelMock->expects(self::once())
            ->method('observations')
            ->willReturn('test');

        $observationsMock = $this->createMock(EmployeeObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $modelMock->expects(self::once())
            ->method('image')
            ->willReturn('image.jpg');

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('setValue')
            ->with('image.jpg')
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $modelMock->expects(self::once())
            ->method('institutionId')
            ->willReturn(2);

        $institutionIdMock = $this->createMock(EmployeeInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $userMock = $dataUser;
        if (!is_null($dataUser)) {
            $userMock = $this->createMock($dataUser);
            $userMock->expects(self::once())
                ->method('id')
                ->willReturn(1);

            $userIdMock = $this->createMock(EmployeeUserId::class);
            $userIdMock->expects(self::once())
                ->method('setValue')
                ->with(1)
                ->willReturnSelf();
            $employeeMock->expects(self::once())
                ->method('userId')
                ->willReturn($userIdMock);
        }

        $relationMock = $this->mock(HasOne::class);
        $relationMock->shouldReceive('first')
            ->once()
            ->with(['user_id'])
            ->andReturn($userMock);

        $modelMock->expects(self::once())
            ->method('relationWithUser')
            ->willReturn($relationMock);

        $this->factory->expects(self::once())
            ->method('buildEmployee')
            ->with(
                $employeeIdMock,
                $identificationMock,
                $nameMock,
                $lastnameMock,
                $stateMock
            )
            ->willReturn($employeeMock);

        $this->translator->setModel($modelMock);
        $result = $this->translator->toDomain();

        $this->assertInstanceOf(EmployeeDomain::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    public function testSetCollectionShouldReturnSelf(): void
    {
        $result = $this->translator->setCollection([1]);

        $this->assertInstanceOf(EmployeeTranslator::class, $result);
        $this->assertSame($result, $this->translator);
    }

    public function testToDomainCollectionShouldReturnEmployees(): void
    {
        $this->translator->setCollection([1]);
        $result = $this->translator->toDomainCollection();

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertIsArray($result->aggregator());
        $this->assertCount(1, $result->aggregator());
        $this->assertSame([1], $result->aggregator());
    }
}
