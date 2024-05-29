<?php

namespace Tests\Feature\Core\Institution\Domain;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
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

    public function test_id_should_return_value_object(): void
    {
        $result = $this->institution->id();

        $this->assertInstanceOf(InstitutionId::class, $result);
        $this->assertSame($this->id, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setId_should_return_self(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $result = $this->institution->setId($idMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($idMock, $result->id());
    }

    public function test_name_should_return_value_object(): void
    {
        $result = $this->institution->name();

        $this->assertInstanceOf(InstitutionName::class, $result);
        $this->assertSame($this->name, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setName_should_return_self(): void
    {
        $name = $this->createMock(InstitutionName::class);
        $result = $this->institution->setName($name);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($name, $result->name());
    }

    public function test_shortname_should_return_value_object(): void
    {
        $result = $this->institution->shortname();

        $this->assertInstanceOf(InstitutionShortname::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setShortname_should_return_self(): void
    {
        $shortname = $this->createMock(InstitutionShortname::class);
        $result = $this->institution->setShortname($shortname);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($shortname, $result->shortname());
    }

    public function test_code_should_return_value_object(): void
    {
        $result = $this->institution->code();

        $this->assertInstanceOf(InstitutionCode::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setCode_should_return_self(): void
    {
        $codeMock = $this->createMock(InstitutionCode::class);
        $result = $this->institution->setCode($codeMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($codeMock, $result->code());
    }

    public function test_logo_should_return_value_object(): void
    {
        $result = $this->institution->logo();

        $this->assertInstanceOf(InstitutionLogo::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setLogo_should_return_self(): void
    {
        $logoMock = $this->createMock(InstitutionLogo::class);
        $result = $this->institution->setLogo($logoMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($logoMock, $result->logo());
    }

    public function test_observations_should_return_value_object(): void
    {
        $result = $this->institution->observations();

        $this->assertInstanceOf(InstitutionObservations::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setObservations_should_return_self(): void
    {
        $observations = $this->createMock(InstitutionObservations::class);
        $result = $this->institution->setObservations($observations);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($observations, $result->observations());
    }

    public function test_state_should_return_value_object(): void
    {
        $result = $this->institution->state();

        $this->assertInstanceOf(InstitutionState::class, $result);
        $this->assertSame(1, $result->value());
    }

    /**
     * @throws Exception
     */
    public function test_setState_should_return_self(): void
    {
        $stateMock = $this->createMock(InstitutionState::class);
        $result = $this->institution->setState($stateMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($stateMock, $result->state());
    }

    public function test_search_should_return_value_object(): void
    {
        $result = $this->institution->search();

        $this->assertInstanceOf(InstitutionSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setSearch_should_return_self(): void
    {
        $searchMock = $this->createMock(InstitutionSearch::class);
        $result = $this->institution->setSearch($searchMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($searchMock, $result->search());
    }

    public function test_createdAt_should_return_value_object(): void
    {
        $result = $this->institution->createdAt();

        $this->assertInstanceOf(InstitutionCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setCreatedAt_should_return_self(): void
    {
        $createdAt = $this->createMock(InstitutionCreatedAt::class);
        $result = $this->institution->setCreatedAt($createdAt);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function test_updatedAt_should_return_value_object(): void
    {
        $result = $this->institution->updatedAt();

        $this->assertInstanceOf(InstitutionUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setUpdatedAt_should_return_self(): void
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
    public function test_refreshSearch_should_update_and_return_self(): void
    {
        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn('code');
        $this->institution->setCode($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $this->institution->setName($nameMock);

        $shortname = $this->createMock(InstitutionShortname::class);
        $shortname->expects(self::once())
            ->method('value')
            ->willReturn('shortname');
        $this->institution->setShortname($shortname);

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('observations');
        $this->institution->setObservations($observations);

        $searchMock = $this->createMock(InstitutionSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with('code name shortname observations')
            ->willReturnSelf();
        $this->institution->setSearch($searchMock);

        $result = $this->institution->refreshSearch();

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
    }
}
