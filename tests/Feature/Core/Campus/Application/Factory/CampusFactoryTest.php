<?php

namespace Tests\Feature\Core\Campus\Application\Factory;

use Core\Campus\Application\Factory\CampusFactory;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\ValueObjects\CampusAddress;
use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use Core\Campus\Domain\ValueObjects\CampusEmail;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Domain\ValueObjects\CampusName;
use Core\Campus\Domain\ValueObjects\CampusObservations;
use Core\Campus\Domain\ValueObjects\CampusPhone;
use Core\Campus\Domain\ValueObjects\CampusSearch;
use Core\Campus\Domain\ValueObjects\CampusState;
use Core\Campus\Domain\ValueObjects\CampusUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\Campus\Application\DataProvider\CampusFactoryDataProvider;
use Tests\TestCase;

#[CoversClass(CampusFactory::class)]
class CampusFactoryTest extends TestCase
{
    private CampusFactory $campusFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->campusFactory = new CampusFactory();
    }

    public function tearDown(): void
    {
        unset($this->campusFactory);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testBuildCampusShouldReturnObject(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusInstitutionIdMock = $this->createMock(CampusInstitutionId::class);
        $campusNameMock = $this->createMock(CampusName::class);

        $result = $this->campusFactory->buildCampus($campusIdMock, $campusInstitutionIdMock, $campusNameMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusIdMock, $result->id());
        $this->assertSame($campusInstitutionIdMock, $result->institutionId());
        $this->assertSame($campusNameMock, $result->name());
    }

    /**
     * @param array<int|string, mixed> $dataTest
     *
     * @throws \Exception
     */
    #[DataProviderExternal(CampusFactoryDataProvider::class, 'provider_dataArray')]
    public function testBuildCampusFromArrayShouldReturnObject(array $dataTest): void
    {
        $result = $this->campusFactory->buildCampusFromArray($dataTest);
        $this->assertInstanceOf(Campus::class, $result);
    }

    public function testBuildCampusAddressShouldReturnObject(): void
    {
        $result = $this->campusFactory->buildCampusAddress('address');

        $this->assertInstanceOf(CampusAddress::class, $result);
        $this->assertSame('address', $result->value());
    }

    public function testBuildCampusPhoneShouldReturnObjectWhenPhoneIsString(): void
    {
        $result = $this->campusFactory->buildCampusPhone('testing');

        $this->assertInstanceOf(CampusPhone::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function testBuildCampusPhoneShouldReturnObjectWhenPhoneIsNull(): void
    {
        $result = $this->campusFactory->buildCampusPhone();

        $this->assertInstanceOf(CampusPhone::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildCampusEmailShouldReturnObjectWhenEmailIsString(): void
    {
        $result = $this->campusFactory->buildCampusEmail('testing@test.com');

        $this->assertInstanceOf(CampusEmail::class, $result);
        $this->assertSame('testing@test.com', $result->value());
    }

    public function testBuildCampusEmailShouldReturnObjectWhenEmailIsNull(): void
    {
        $result = $this->campusFactory->buildCampusEmail();

        $this->assertInstanceOf(CampusEmail::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildCampusEmailShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusEmail> does not allow the invalid email: <testing>.');

        $this->campusFactory->buildCampusEmail('testing');
    }

    public function testBuildCampusObservationsShouldReturnObjectWhenObservationsIsString(): void
    {
        $result = $this->campusFactory->buildCampusObservations('testing');

        $this->assertInstanceOf(CampusObservations::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function testBuildCampusObservationsShouldReturnObjectWhenObservationsIsNull(): void
    {
        $result = $this->campusFactory->buildCampusObservations();

        $this->assertInstanceOf(CampusObservations::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildCampusSearchShouldReturnObjectWhenSearchIsString(): void
    {
        $result = $this->campusFactory->buildCampusSearch('testing');

        $this->assertInstanceOf(CampusSearch::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function testBuildCampusSearchShouldReturnObjectWhenSearchIsNull(): void
    {
        $result = $this->campusFactory->buildCampusSearch();

        $this->assertInstanceOf(CampusSearch::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws \Exception
     */
    public function testBuildCampusStateShouldReturnObject(): void
    {
        $result = $this->campusFactory->buildCampusState(1);

        $this->assertInstanceOf(CampusState::class, $result);
        $this->assertSame(1, $result->value());
    }

    public function testBuildCampusStateShouldReturnException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusState> does not allow the invalid state: <5>.');

        $this->campusFactory->buildCampusState(5);
    }

    public function testBuildCampusCreatedAtShouldReturnObject(): void
    {
        $datetime = new \DateTime('2024-06-25 8:13:00');
        $result = $this->campusFactory->buildCampusCreatedAt($datetime);

        $this->assertInstanceOf(CampusCreatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function testBuildCampusUpdatedAtShouldReturnObjectWhereDateIsNotNull(): void
    {
        $datetime = new \DateTime('2024-06-25 8:13:00');
        $result = $this->campusFactory->buildCampusUpdatedAt($datetime);

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function testBuildCampusUpdatedAtShouldReturnObjectWhereDateIsNull(): void
    {
        $result = $this->campusFactory->buildCampusUpdatedAt();

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws Exception
     */
    public function testBuildCampusCollectionShouldReturnObject(): void
    {
        $campusMock1 = $this->createMock(Campus::class);
        $campusMock2 = $this->createMock(Campus::class);
        $campusMock3 = $this->createMock(Campus::class);

        $result = $this->campusFactory->buildCampusCollection($campusMock1, $campusMock2, $campusMock3);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertCount(3, $result->items());
    }
}
