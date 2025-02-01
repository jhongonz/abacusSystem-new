<?php

namespace Tests\Feature\Core\Campus\Domain;

use Core\Campus\Domain\Campus;
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
use Core\SharedContext\Model\ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ArrayIterator::class)]
#[CoversClass(Campus::class)]
class CampusTest extends TestCase
{
    private CampusId|MockObject $campusId;
    private CampusInstitutionId|MockObject $campusInstitutionId;
    private CampusName|MockObject $campusName;
    private Campus $campus;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusId = $this->createMock(CampusId::class);
        $this->campusInstitutionId = $this->createMock(CampusInstitutionId::class);
        $this->campusName = $this->createMock(CampusName::class);
        $this->campus = new Campus(
            $this->campusId,
            $this->campusInstitutionId,
            $this->campusName
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->campus,
            $this->campusId,
            $this->campusName,
            $this->campusInstitutionId
        );
        parent::tearDown();
    }

    public function testIdShouldReturnValueObject(): void
    {
        $result = $this->campus->id();

        $this->assertInstanceOf(CampusId::class, $result);
        $this->assertSame($this->campusId, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetIdShouldChangeAndReturnSelf(): void
    {
        $campusId = $this->createMock(CampusId::class);
        $result = $this->campus->setId($campusId);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($campusId, $result->id());
    }

    public function testInstitutionIdShouldReturnValueObject(): void
    {
        $result = $this->campus->institutionId();

        $this->assertInstanceOf(CampusInstitutionId::class, $result);
        $this->assertSame($this->campusInstitutionId, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetInstitutionIdShouldChangeAndReturnSelf(): void
    {
        $campusInstitutionId = $this->createMock(CampusInstitutionId::class);
        $result = $this->campus->setInstitutionId($campusInstitutionId);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($campusInstitutionId, $result->institutionId());
    }

    public function testNameShouldReturnValueObject(): void
    {
        $result = $this->campus->name();

        $this->assertInstanceOf(CampusName::class, $result);
        $this->assertSame($this->campusName, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetNameShouldChangeAndReturnSelf(): void
    {
        $nameMock = $this->createMock(CampusName::class);
        $result = $this->campus->setName($nameMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($nameMock, $result->name());
    }

    public function testAddressShouldReturnValueObject(): void
    {
        $result = $this->campus->address();
        $this->assertInstanceOf(CampusAddress::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetAddressShouldChangeAndReturnSelf(): void
    {
        $addressMock = $this->createMock(CampusAddress::class);
        $result = $this->campus->setAddress($addressMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($addressMock, $result->address());
    }

    public function testPhoneShouldReturnValueObject(): void
    {
        $result = $this->campus->phone();
        $this->assertInstanceOf(CampusPhone::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetPhoneShouldChangeAndReturnSelf(): void
    {
        $phoneMock = $this->createMock(CampusPhone::class);
        $result = $this->campus->setPhone($phoneMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($phoneMock, $result->phone());
    }

    public function testEmailShouldReturnValueObject(): void
    {
        $result = $this->campus->email();
        $this->assertInstanceOf(CampusEmail::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetEmailShouldChangeAndReturnSelf(): void
    {
        $emailMock = $this->createMock(CampusEmail::class);
        $result = $this->campus->setEmail($emailMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($emailMock, $result->email());
    }

    public function testObservationsShouldReturnValueObject(): void
    {
        $result = $this->campus->observations();
        $this->assertInstanceOf(CampusObservations::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetObservationsShouldChangeAndReturnSelf(): void
    {
        $observationsMock = $this->createMock(CampusObservations::class);
        $result = $this->campus->setObservations($observationsMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($observationsMock, $result->observations());
    }

    public function testSearchShouldReturnValueObject(): void
    {
        $result = $this->campus->search();
        $this->assertInstanceOf(CampusSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetSearchShouldChangeAndReturnSelf(): void
    {
        $searchMock = $this->createMock(CampusSearch::class);
        $result = $this->campus->setSearch($searchMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($searchMock, $result->search());
    }

    public function testStateShouldReturnValueObject(): void
    {
        $result = $this->campus->state();
        $this->assertInstanceOf(CampusState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetStateShouldChangeAndReturnSelf(): void
    {
        $stateMock = $this->createMock(CampusState::class);
        $result = $this->campus->setState($stateMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($stateMock, $result->state());
    }

    public function testCreatedAtShouldReturnValueObject(): void
    {
        $result = $this->campus->createdAt();
        $this->assertInstanceOf(CampusCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetCreatedAtShouldChangeAndReturnSelf(): void
    {
        $createdAt = $this->createMock(CampusCreatedAt::class);
        $result = $this->campus->setCreatedAt($createdAt);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function testUpdatedAtShouldReturnValueObject(): void
    {
        $result = $this->campus->updatedAt();
        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetUpdatedAtShouldChangeAndReturnSelf(): void
    {
        $updatedAt = $this->createMock(CampusUpdatedAt::class);
        $result = $this->campus->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($updatedAt, $result->updatedAt());
    }

    /**
     * @throws Exception
     */
    public function testRefreshSearchShouldReturnSelf(): void
    {
        $this->campusName->expects(self::once())
            ->method('value')
            ->willReturn(' Name ');

        $address = $this->createMock(CampusAddress::class);
        $address->expects(self::once())
            ->method('value')
            ->willReturn(' addreSS ');
        $this->campus->setAddress($address);

        $phone = $this->createMock(CampusPhone::class);
        $phone->expects(self::once())
            ->method('value')
            ->willReturn(' phOne');
        $this->campus->setPhone($phone);

        $email = $this->createMock(CampusEmail::class);
        $email->expects(self::once())
            ->method('value')
            ->willReturn('emAil ');
        $this->campus->setEmail($email);

        $observations = $this->createMock(CampusObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('oBservations ');
        $this->campus->setObservations($observations);

        $search = new CampusSearch();
        $this->campus->setSearch($search);

        $dataExpected = 'name address phone email observations';
        $result = $this->campus->refreshSearch();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($search, $result->search());
        $this->assertSame($dataExpected, $result->search()->value());
    }
}
