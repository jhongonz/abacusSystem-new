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
        $this->campusFactory = new CampusFactory;
    }

    public function tearDown(): void
    {
        unset($this->campusFactory);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_buildCampus_should_return_object(): void
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
     * @throws \Exception
     */
    #[DataProviderExternal(CampusFactoryDataProvider::class, 'provider_dataArray')]
    public function test_buildCampusFromArray_should_return_object(array $dataTest): void
    {
        $result = $this->campusFactory->buildCampusFromArray($dataTest);
        $this->assertInstanceOf(Campus::class, $result);
    }

    public function test_buildCampusAddress_should_return_object(): void
    {
        $result = $this->campusFactory->buildCampusAddress('address');

        $this->assertInstanceOf(CampusAddress::class, $result);
        $this->assertSame('address', $result->value());
    }

    public function test_buildCampusPhone_should_return_object_when_phone_is_string(): void
    {
        $result = $this->campusFactory->buildCampusPhone('testing');

        $this->assertInstanceOf(CampusPhone::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function test_buildCampusPhone_should_return_object_when_phone_is_null(): void
    {
        $result = $this->campusFactory->buildCampusPhone();

        $this->assertInstanceOf(CampusPhone::class, $result);
        $this->assertNull($result->value());
    }

    public function test_buildCampusEmail_should_return_object_when_email_is_string(): void
    {
        $result = $this->campusFactory->buildCampusEmail('testing@test.com');

        $this->assertInstanceOf(CampusEmail::class, $result);
        $this->assertSame('testing@test.com', $result->value());
    }

    public function test_buildCampusEmail_should_return_object_when_email_is_null(): void
    {
        $result = $this->campusFactory->buildCampusEmail();

        $this->assertInstanceOf(CampusEmail::class, $result);
        $this->assertNull($result->value());
    }

    public function test_buildCampusEmail_should_return_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusEmail> does not allow the invalid email: <testing>.');

        $this->campusFactory->buildCampusEmail('testing');
    }

    public function test_buildCampusObservations_should_return_object_when_observations_is_string(): void
    {
        $result = $this->campusFactory->buildCampusObservations('testing');

        $this->assertInstanceOf(CampusObservations::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function test_buildCampusObservations_should_return_object_when_observations_is_null(): void
    {
        $result = $this->campusFactory->buildCampusObservations();

        $this->assertInstanceOf(CampusObservations::class, $result);
        $this->assertNull($result->value());
    }

    public function test_buildCampusSearch_should_return_object_when_search_is_string(): void
    {
        $result = $this->campusFactory->buildCampusSearch('testing');

        $this->assertInstanceOf(CampusSearch::class, $result);
        $this->assertSame('testing', $result->value());
    }

    public function test_buildCampusSearch_should_return_object_when_search_is_null(): void
    {
        $result = $this->campusFactory->buildCampusSearch();

        $this->assertInstanceOf(CampusSearch::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws \Exception
     */
    public function test_buildCampusState_should_return_object(): void
    {
        $result = $this->campusFactory->buildCampusState(1);

        $this->assertInstanceOf(CampusState::class, $result);
        $this->assertSame(1, $result->value());
    }

    public function test_buildCampusState_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusState> does not allow the invalid state: <5>.');

        $this->campusFactory->buildCampusState(5);
    }

    public function test_buildCampusCreatedAt_should_return_object(): void
    {
        $datetime = new \DateTime('2024-06-25 8:13:00');
        $result = $this->campusFactory->buildCampusCreatedAt($datetime);

        $this->assertInstanceOf(CampusCreatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function test_buildCampusUpdatedAt_should_return_object_where_date_is_not_null(): void
    {
        $datetime = new \DateTime('2024-06-25 8:13:00');
        $result = $this->campusFactory->buildCampusUpdatedAt($datetime);

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function test_buildCampusUpdatedAt_should_return_object_where_date_is_null(): void
    {
        $result = $this->campusFactory->buildCampusUpdatedAt();

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @throws Exception
     */
    public function test_buildCampusCollection_should_return_object(): void
    {
        $campusMock1 = $this->createMock(Campus::class);
        $campusMock2 = $this->createMock(Campus::class);
        $campusMock3 = $this->createMock(Campus::class);

        $result = $this->campusFactory->buildCampusCollection($campusMock1, $campusMock2, $campusMock3);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertCount(3, $result->items());
    }
}
