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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

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

    public function test_id_should_return_value_object(): void
    {
        $result = $this->campus->id();

        $this->assertInstanceOf(CampusId::class, $result);
        $this->assertSame($this->campusId, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setId_should_change_and_return_self(): void
    {
        $campusId = $this->createMock(CampusId::class);
        $result = $this->campus->setId($campusId);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($campusId, $result->id());
    }

    public function test_institutionId_should_return_value_object(): void
    {
        $result = $this->campus->institutionId();

        $this->assertInstanceOf(CampusInstitutionId::class, $result);
        $this->assertSame($this->campusInstitutionId, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setInstitutionId_should_change_and_return_self(): void
    {
        $campusInstitutionId = $this->createMock(CampusInstitutionId::class);
        $result = $this->campus->setInstitutionId($campusInstitutionId);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($campusInstitutionId, $result->institutionId());
    }

    public function test_name_should_return_value_object(): void
    {
        $result = $this->campus->name();

        $this->assertInstanceOf(CampusName::class, $result);
        $this->assertSame($this->campusName, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setName_should_change_and_return_self(): void
    {
        $nameMock = $this->createMock(CampusName::class);
        $result = $this->campus->setName($nameMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($nameMock, $result->name());
    }

    public function test_address_should_return_value_object(): void
    {
        $result = $this->campus->address();
        $this->assertInstanceOf(CampusAddress::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setAddress_should_change_and_return_self(): void
    {
        $addressMock = $this->createMock(CampusAddress::class);
        $result = $this->campus->setAddress($addressMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($addressMock, $result->address());
    }

    public function test_phone_should_return_value_object(): void
    {
        $result = $this->campus->phone();
        $this->assertInstanceOf(CampusPhone::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setPhone_should_change_and_return_self(): void
    {
        $phoneMock = $this->createMock(CampusPhone::class);
        $result = $this->campus->setPhone($phoneMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($phoneMock, $result->phone());
    }

    public function test_email_should_return_value_object(): void
    {
        $result = $this->campus->email();
        $this->assertInstanceOf(CampusEmail::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setEmail_should_change_and_return_self(): void
    {
        $emailMock = $this->createMock(CampusEmail::class);
        $result = $this->campus->setEmail($emailMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($emailMock, $result->email());
    }

    public function test_observations_should_return_value_object(): void
    {
        $result = $this->campus->observations();
        $this->assertInstanceOf(CampusObservations::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setObservations_should_change_and_return_self(): void
    {
        $observationsMock = $this->createMock(CampusObservations::class);
        $result = $this->campus->setObservations($observationsMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($observationsMock, $result->observations());
    }

    public function test_search_should_return_value_object(): void
    {
        $result = $this->campus->search();
        $this->assertInstanceOf(CampusSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setSearch_should_change_and_return_self(): void
    {
        $searchMock = $this->createMock(CampusSearch::class);
        $result = $this->campus->setSearch($searchMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($searchMock, $result->search());
    }

    public function test_state_should_return_value_object(): void
    {
        $result = $this->campus->state();
        $this->assertInstanceOf(CampusState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setState_should_change_and_return_self(): void
    {
        $stateMock = $this->createMock(CampusState::class);
        $result = $this->campus->setState($stateMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($stateMock, $result->state());
    }

    public function test_createdAt_should_return_value_object(): void
    {
        $result = $this->campus->createdAt();
        $this->assertInstanceOf(CampusCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setCreatedAt_should_change_and_return_self(): void
    {
        $createdAt = $this->createMock(CampusCreatedAt::class);
        $result = $this->campus->setCreatedAt($createdAt);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function test_updatedAt_should_return_value_object(): void
    {
        $result = $this->campus->updatedAt();
        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setUpdatedAt_should_change_and_return_self(): void
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
    public function test_refreshSearch_should_return_self(): void
    {
        $this->campusName->expects(self::once())
            ->method('value')
            ->willReturn('name');

        $address = $this->createMock(CampusAddress::class);
        $address->expects(self::once())
            ->method('value')
            ->willReturn('address');
        $this->campus->setAddress($address);

        $phone = $this->createMock(CampusPhone::class);
        $phone->expects(self::once())
            ->method('value')
            ->willReturn('phone');
        $this->campus->setPhone($phone);

        $email = $this->createMock(CampusEmail::class);
        $email->expects(self::once())
            ->method('value')
            ->willReturn('email');
        $this->campus->setEmail($email);

        $observations = $this->createMock(CampusObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('observations');
        $this->campus->setObservations($observations);

        $search = $this->createMock(CampusSearch::class);
        $search->expects(self::once())
            ->method('setValue')
            ->with('name address phone email observations')
            ->willReturnSelf();
        $this->campus->setSearch($search);

        $result = $this->campus->refreshSearch();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($this->campus, $result);
    }
}
