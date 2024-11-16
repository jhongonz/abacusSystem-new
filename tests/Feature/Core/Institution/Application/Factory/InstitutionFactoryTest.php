<?php

namespace Tests\Feature\Core\Institution\Application\Factory;

use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionSearch;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\Institution\Application\Factory\DataProvider\DataProviderInstitutionFactory;
use Tests\TestCase;

#[CoversClass(InstitutionFactory::class)]
class InstitutionFactoryTest extends TestCase
{
    private InstitutionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new InstitutionFactory();
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderInstitutionFactory::class, 'provider')]
    public function testBuildInstitutionFromArrayShouldReturnObject(array $data): void
    {
        $result = $this->factory->buildInstitutionFromArray($data);
        $this->assertInstanceOf(Institution::class, $result);
    }

    public function testBuildInstitutionCodeShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionCode('code');

        $this->assertInstanceOf(InstitutionCode::class, $result);
        $this->assertSame('code', $result->value());
    }

    public function testBuildInstitutionShortnameShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionShortname('shortname');

        $this->assertInstanceOf(InstitutionShortname::class, $result);
        $this->assertSame('shortname', $result->value());
    }

    public function testBuildInstitutionLogoShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionLogo('logo');

        $this->assertInstanceOf(InstitutionLogo::class, $result);
        $this->assertSame('logo', $result->value());
    }

    public function testBuildInstitutionObservationsShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionObservations('observations');

        $this->assertInstanceOf(InstitutionObservations::class, $result);
        $this->assertSame('observations', $result->value());
    }

    /**
     * @throws \Exception
     */
    public function testBuildInstitutionStateShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionState(1);

        $this->assertInstanceOf(InstitutionState::class, $result);
        $this->assertSame(1, $result->value());
    }

    public function testBuildInstitutionSearchShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionSearch('search');

        $this->assertInstanceOf(InstitutionSearch::class, $result);
        $this->assertSame('search', $result->value());
    }

    public function testBuildInstitutionCreatedAtShouldReturnObject(): void
    {
        $datetime = new \DateTime();
        $result = $this->factory->buildInstitutionCreatedAt($datetime);

        $this->assertInstanceOf(InstitutionCreatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    public function testBuildInstitutionUpdatedAtShouldReturnObject(): void
    {
        $datetime = new \DateTime();
        $result = $this->factory->buildInstitutionUpdatedAt($datetime);

        $this->assertInstanceOf(InstitutionUpdatedAt::class, $result);
        $this->assertSame($datetime, $result->value());
    }

    /**
     * @throws Exception
     */
    public function testBuildInstitutionsShouldReturnObject(): void
    {
        $institutionMock = $this->createMock(Institution::class);
        $result = $this->factory->buildInstitutions($institutionMock);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertCount(1, $result->items());
    }
}
