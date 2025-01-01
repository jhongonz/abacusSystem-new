<?php

namespace Tests\Feature\Core\Institution\Application\Factory;

use Core\Institution\Application\Factory\InstitutionFactory;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
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
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Institution\Application\Factory\DataProvider\DataProviderInstitutionFactory;
use Tests\TestCase;

#[CoversClass(InstitutionFactory::class)]
class InstitutionFactoryTest extends TestCase
{
    private InstitutionFactory|MockObject $factory;

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
     * @param array<string, mixed> $dataProvider
     *
     * @throws \Exception
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderInstitutionFactory::class, 'provider')]
    public function testBuildInstitutionFromArrayShouldReturnObject(array $dataProvider): void
    {
        $dataExpected = $dataProvider[Institution::TYPE];
        $this->factory = $this->getMockBuilder(InstitutionFactory::class)
            ->onlyMethods(['buildInstitutionId', 'buildInstitutionName', 'buildInstitution'])
            ->getMock();

        $institutionMock = $this->createMock(Institution::class);

        $shortnameMock = $this->createMock(InstitutionShortname::class);
        $shortnameMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['shortname'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('shortname')
            ->willReturn($shortnameMock);

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['code'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $observationsMock = $this->createMock(InstitutionObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['observations'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['state'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $addressMock = $this->createMock(InstitutionAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['address'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $phoneMock = $this->createMock(InstitutionPhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['phone'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(InstitutionEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with($dataExpected['email'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        if (!is_null($dataExpected['createdAt'])) {
            $createdAtMock = $this->createMock(InstitutionCreatedAt::class);
            $createdAtMock->expects(self::once())
                ->method('setValue')
                ->with(new \DateTime($dataExpected['createdAt']))
                ->willReturnSelf();
            $institutionMock->expects(self::once())
                ->method('createdAt')
                ->willReturn($createdAtMock);
        }

        if (!is_null($dataExpected['updatedAt'])) {
            $updatedAtMock = $this->createMock(InstitutionUpdatedAt::class);
            $updatedAtMock->expects(self::once())
                ->method('setValue')
                ->with(new \DateTime($dataExpected['updatedAt']))
                ->willReturnSelf();
            $institutionMock->expects(self::once())
                ->method('updatedAt')
                ->willReturn($updatedAtMock);
        }

        if (!is_null($dataExpected['logo'])) {
            $logoMock = $this->createMock(InstitutionLogo::class);
            $logoMock->expects(self::once())
                ->method('setValue')
                ->with($dataExpected['logo'])
                ->willReturnSelf();
            $institutionMock->expects(self::once())
                ->method('logo')
                ->willReturn($logoMock);
        }

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionId')
            ->with($dataExpected['id'])
            ->willReturn($institutionIdMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionName')
            ->with($dataExpected['name'])
            ->willReturn($nameMock);

        $this->factory->expects(self::once())
            ->method('buildInstitution')
            ->with($institutionIdMock, $nameMock)
            ->willReturn($institutionMock);

        $result = $this->factory->buildInstitutionFromArray($dataProvider);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testBuildInstitutionShouldReturnObject(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $nameMock = $this->createMock(InstitutionName::class);

        $result = $this->factory->buildInstitution($idMock, $nameMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($idMock, $result->id());
        $this->assertSame($nameMock, $result->name());
    }

    public function testBuildInstitutionIdShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionId(10);

        $this->assertInstanceOf(InstitutionId::class, $result);
        $this->assertSame($result->value(), 10);
    }

    public function testBuildInstitutionCodeShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionCode('code');

        $this->assertInstanceOf(InstitutionCode::class, $result);
        $this->assertSame('code', $result->value());
    }

    public function testBuildInstitutionNameShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionName('name');

        $this->assertInstanceOf(InstitutionName::class, $result);
        $this->assertSame('name', $result->value());
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

    public function testBuildInstitutionAddressShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionAddress('address');

        $this->assertInstanceOf(InstitutionAddress::class, $result);
        $this->assertSame('address', $result->value());
    }

    public function testBuildInstitutionAddressShouldReturnObjectWhenIsNull(): void
    {
        $result = $this->factory->buildInstitutionAddress();

        $this->assertInstanceOf(InstitutionAddress::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildInstitutionPhoneShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionPhone('123456789');

        $this->assertInstanceOf(InstitutionPhone::class, $result);
        $this->assertSame('123456789', $result->value());
    }

    public function testBuildInstitutionEmailShouldReturnObject(): void
    {
        $result = $this->factory->buildInstitutionEmail('algo@algo.com');

        $this->assertInstanceOf(InstitutionEmail::class, $result);
        $this->assertSame('algo@algo.com', $result->value());
    }

    public function testBuildInstitutionEmailShouldReturnObjectWhenIsNull(): void
    {
        $result = $this->factory->buildInstitutionEmail();

        $this->assertInstanceOf(InstitutionEmail::class, $result);
        $this->assertNull($result->value());
    }
}
