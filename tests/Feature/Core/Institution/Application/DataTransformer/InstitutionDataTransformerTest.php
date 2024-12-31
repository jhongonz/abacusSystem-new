<?php

namespace Tests\Feature\Core\Institution\Application\DataTransformer;

use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
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
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Institution\Application\DataProvider\InstitutionDataTransformerDataProvider;
use Tests\TestCase;

#[CoversClass(InstitutionDataTransformer::class)]
class InstitutionDataTransformerTest extends TestCase
{
    private Institution|MockObject $institution;
    private InstitutionDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institution = $this->createMock(Institution::class);
        $this->dataTransformer = new InstitutionDataTransformer();
    }

    public function tearDown(): void
    {
        unset(
            $this->dataTransformer,
            $this->institution
        );
        parent::tearDown();
    }

    public function testWriteShouldReturnSelf(): void
    {
        $result = $this->dataTransformer->write($this->institution);

        $this->assertInstanceOf(InstitutionDataTransformer::class, $result);
        $this->assertSame($this->dataTransformer, $result);
    }

    /**
     * @param array<string, array<string, mixed>> $dataProvider
     *
     * @throws Exception
     */
    #[DataProviderExternal(InstitutionDataTransformerDataProvider::class, 'provider_read')]
    public function testReadShouldReturnArray(array $dataProvider): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['id']);
        $this->institution->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['code']);
        $this->institution->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['name']);
        $this->institution->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $shortname = $this->createMock(InstitutionShortname::class);
        $shortname->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['shortname']);
        $this->institution->expects(self::once())
            ->method('shortname')
            ->willReturn($shortname);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['logo']);
        $this->institution->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['observations']);
        $this->institution->expects(self::once())
            ->method('observations')
            ->willReturn($observations);

        $addressMock = $this->createMock(InstitutionAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['address']);
        $this->institution->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $phoneMock = $this->createMock(InstitutionPhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['phone']);
        $this->institution->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(InstitutionEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['email']);
        $this->institution->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['state']);
        $this->institution->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $searchMock = $this->createMock(InstitutionSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['search']);
        $this->institution->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $createdAt = $this->createMock(InstitutionCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($dataProvider['createdAt']);
        $this->institution->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(InstitutionUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($dataProvider['updatedAt']);
        $this->institution->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        if (empty($dataProvider['updatedAt'])) {
            $dataProvider['updatedAt'] = null;
        }

        $this->dataTransformer->write($this->institution);
        $result = $this->dataTransformer->read();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(Institution::TYPE, $result);
        $this->assertSame([Institution::TYPE => $dataProvider], $result);
    }

    /**
     * @param array<string, array<string, mixed>> $dataProvider
     *
     * @throws Exception
     */
    #[DataProviderExternal(InstitutionDataTransformerDataProvider::class, 'provider_readToShare')]
    public function testReadToShareShouldReturnArray(array $dataProvider): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['id']);
        $this->institution->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['code']);
        $this->institution->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['name']);
        $this->institution->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $shortname = $this->createMock(InstitutionShortname::class);
        $shortname->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['shortname']);
        $this->institution->expects(self::once())
            ->method('shortname')
            ->willReturn($shortname);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['logo']);
        $this->institution->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['observations']);
        $this->institution->expects(self::once())
            ->method('observations')
            ->willReturn($observations);

        $addressMock = $this->createMock(InstitutionAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['address']);
        $this->institution->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $phoneMock = $this->createMock(InstitutionPhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['phone']);
        $this->institution->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(InstitutionEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['email']);
        $this->institution->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['state']);
        $stateMock->expects(self::once())
            ->method('formatHtmlToState')
            ->willReturn($dataProvider['state_literal']);
        $this->institution->expects(self::exactly(2))
            ->method('state')
            ->willReturn($stateMock);

        $searchMock = $this->createMock(InstitutionSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn($dataProvider['search']);
        $this->institution->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $createdAt = $this->createMock(InstitutionCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($dataProvider['createdAt']);
        $this->institution->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(InstitutionUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($dataProvider['updatedAt']);
        $this->institution->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->dataTransformer->write($this->institution);
        $result = $this->dataTransformer->readToShare();

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey(Institution::TYPE, $result);
        $this->assertSame($dataProvider, $result);
    }
}
