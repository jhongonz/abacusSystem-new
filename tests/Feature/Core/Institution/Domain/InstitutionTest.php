<?php

namespace Tests\Feature\Core\Institution\Domain;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionAddress;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionEmail;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionPhone;
use Core\Institution\Domain\ValueObjects\InstitutionSearch;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Institution::class)]
class InstitutionTest extends TestCase
{
    private InstitutionId|MockObject $id;
    private InstitutionName|MockObject $name;
    private Institution $institution;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->id = $this->createMock(InstitutionId::class);
        $this->name = $this->createMock(InstitutionName::class);
        $this->institution = new Institution(
            $this->id,
            $this->name
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->id,
            $this->name,
            $this->institution
        );
        parent::tearDown();
    }

    public function testIdShouldReturnValueObject(): void
    {
        $result = $this->institution->id();

        $this->assertInstanceOf(InstitutionId::class, $result);
        $this->assertSame($this->id, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetIdShouldReturnSelf(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $result = $this->institution->setId($idMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($idMock, $result->id());
    }

    public function testNameShouldReturnValueObject(): void
    {
        $result = $this->institution->name();

        $this->assertInstanceOf(InstitutionName::class, $result);
        $this->assertSame($this->name, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetNameShouldReturnSelf(): void
    {
        $name = $this->createMock(InstitutionName::class);
        $result = $this->institution->setName($name);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($name, $result->name());
    }

    public function testShortnameShouldReturnValueObject(): void
    {
        $result = $this->institution->shortname();

        $this->assertInstanceOf(InstitutionShortname::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetShortnameShouldReturnSelf(): void
    {
        $shortname = $this->createMock(InstitutionShortname::class);
        $result = $this->institution->setShortname($shortname);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($shortname, $result->shortname());
    }

    public function testCodeShouldReturnValueObject(): void
    {
        $result = $this->institution->code();

        $this->assertInstanceOf(InstitutionCode::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetCodeShouldReturnSelf(): void
    {
        $codeMock = $this->createMock(InstitutionCode::class);
        $result = $this->institution->setCode($codeMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($codeMock, $result->code());
    }

    public function testLogoShouldReturnValueObject(): void
    {
        $result = $this->institution->logo();

        $this->assertInstanceOf(InstitutionLogo::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetLogoShouldReturnSelf(): void
    {
        $logoMock = $this->createMock(InstitutionLogo::class);
        $result = $this->institution->setLogo($logoMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($logoMock, $result->logo());
    }

    public function testObservationsShouldReturnValueObject(): void
    {
        $result = $this->institution->observations();

        $this->assertInstanceOf(InstitutionObservations::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetObservationsShouldReturnSelf(): void
    {
        $observations = $this->createMock(InstitutionObservations::class);
        $result = $this->institution->setObservations($observations);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($observations, $result->observations());
    }

    public function testAddressShouldReturnValueObject(): void
    {
        $result = $this->institution->address();

        $this->assertInstanceOf(InstitutionAddress::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetAddressShouldReturnSelf(): void
    {
        $address = $this->createMock(InstitutionAddress::class);
        $result = $this->institution->setAddress($address);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($address, $result->address());
    }

    public function testStateShouldReturnValueObject(): void
    {
        $result = $this->institution->state();

        $this->assertInstanceOf(InstitutionState::class, $result);
        $this->assertSame(1, $result->value());
    }

    /**
     * @throws Exception
     */
    public function testSetStateShouldReturnSelf(): void
    {
        $stateMock = $this->createMock(InstitutionState::class);
        $result = $this->institution->setState($stateMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($stateMock, $result->state());
    }

    public function testSearchShouldReturnValueObject(): void
    {
        $result = $this->institution->search();

        $this->assertInstanceOf(InstitutionSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetSearchShouldReturnSelf(): void
    {
        $searchMock = $this->createMock(InstitutionSearch::class);
        $result = $this->institution->setSearch($searchMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($searchMock, $result->search());
    }

    public function testCreatedAtShouldReturnValueObject(): void
    {
        $result = $this->institution->createdAt();

        $this->assertInstanceOf(InstitutionCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetCreatedAtShouldReturnSelf(): void
    {
        $createdAt = $this->createMock(InstitutionCreatedAt::class);
        $result = $this->institution->setCreatedAt($createdAt);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function testUpdatedAtShouldReturnValueObject(): void
    {
        $result = $this->institution->updatedAt();

        $this->assertInstanceOf(InstitutionUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetUpdatedAtShouldReturnSelf(): void
    {
        $updatedAt = $this->createMock(InstitutionUpdatedAt::class);
        $result = $this->institution->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($updatedAt, $result->updatedAt());
    }

    /**
     * @throws Exception
     */
    public function testRefreshSearchShouldUpdateAndReturnSelf(): void
    {
        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn('coDe ');
        $this->institution->setCode($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('  Name');
        $this->institution->setName($nameMock);

        $shortname = $this->createMock(InstitutionShortname::class);
        $shortname->expects(self::once())
            ->method('value')
            ->willReturn(' shorTname ');
        $this->institution->setShortname($shortname);

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn(' oBservations');
        $this->institution->setObservations($observations);

        $address = $this->createMock(InstitutionAddress::class);
        $address->expects(self::once())
            ->method('value')
            ->willReturn('  adDRess ');
        $this->institution->setAddress($address);

        $phone = $this->createMock(InstitutionPhone::class);
        $phone->expects(self::once())
            ->method('value')
            ->willReturn('Phone  ');
        $this->institution->setPhone($phone);

        $email = $this->createMock(InstitutionEmail::class);
        $email->expects(self::once())
            ->method('value')
            ->willReturn('  EmAil');
        $this->institution->setEmail($email);

        $searchMock = $this->createMock(InstitutionSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with('code name shortname observations address phone email')
            ->willReturnSelf();
        $this->institution->setSearch($searchMock);

        $result = $this->institution->refreshSearch();

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
    }

    public function testPhoneShouldReturnValueObject(): void
    {
        $result = $this->institution->phone();

        $this->assertInstanceOf(InstitutionPhone::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetPhoneShouldReturnSelf(): void
    {
        $phone = $this->createMock(InstitutionPhone::class);
        $result = $this->institution->setPhone($phone);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($phone, $result->phone());
    }

    public function testEmailShouldReturnValueObject(): void
    {
        $result = $this->institution->email();

        $this->assertInstanceOf(InstitutionEmail::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetEmailShouldReturnSelf(): void
    {
        $email = $this->createMock(InstitutionEmail::class);
        $result = $this->institution->setEmail($email);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($email, $result->email());
    }
}
