<?php

namespace Tests\Feature\Core\Campus\Application\DataTransformer;

use Core\Campus\Application\DataTransformer\CampusDataTransformer;
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
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Campus\Application\DataProvider\CampusDataTransformerDataProvider;
use Tests\TestCase;

#[CoversClass(CampusDataTransformer::class)]
class CampusDataTransformerTest extends TestCase
{
    private Campus|MockObject $campus;
    private CampusDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campus = $this->createMock(Campus::class);
        $this->dataTransformer = new CampusDataTransformer;
    }

    public function tearDown(): void
    {
        unset(
            $this->dataTransformer,
            $this->campus
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_write_should_return_self(): void
    {
        $campusMock = $this->createMock(Campus::class);
        $result = $this->dataTransformer->write($campusMock);

        $this->assertInstanceOf(CampusDataTransformer::class, $result);
        $this->assertSame($this->dataTransformer, $result);
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(CampusDataTransformerDataProvider::class, 'provider_read')]
    public function test_read_should_return_array(array $dataTest): void
    {
        $this->prepareCampusMock($dataTest);

        $stateMock = $this->createMock(CampusState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->campus->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $result = $this->dataTransformer->write($this->campus)->read();

        $dataExpected = [Campus::TYPE => $dataTest];
        $this->assertIsArray($result);
        $this->assertSame($dataExpected, $result);
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(CampusDataTransformerDataProvider::class, 'provider_readToShare')]
    public function test_readToSearch_should_return_array(array $dataExpected): void
    {
        $this->prepareCampusMock($dataExpected);

        $stateMock = $this->createMock(CampusState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $stateMock->expects(self::once())
            ->method('formatHtmlToState')
            ->willReturn($dataExpected['state_literal']);
        $this->campus->expects(self::exactly(2))
            ->method('state')
            ->willReturn($stateMock);

        $result = $this->dataTransformer->write($this->campus)->readToShare();

        $this->assertIsArray($result);
        $this->assertSame($dataExpected, $result);
    }

    /**
     * @throws Exception
     */
    private function prepareCampusMock(array $data): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['id']);
        $this->campus->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $institutionIdMock->expects(self::once())
            ->method('value')
            ->willReturn($data['institutionId']);
        $this->campus->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $nameMock = $this->createMock(CampusName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn($data['name']);
        $this->campus->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $addressMock = $this->createMock(CampusAddress::class);
        $addressMock->expects(self::once())
            ->method('value')
            ->willReturn($data['address']);
        $this->campus->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $phoneMock = $this->createMock(CampusPhone::class);
        $phoneMock->expects(self::once())
            ->method('value')
            ->willReturn($data['phone']);
        $this->campus->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $emailMock = $this->createMock(CampusEmail::class);
        $emailMock->expects(self::once())
            ->method('value')
            ->willReturn($data['email']);
        $this->campus->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $observationsMock = $this->createMock(CampusObservations::class);
        $observationsMock->expects(self::once())
            ->method('value')
            ->willReturn($data['observations']);
        $this->campus->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $searchMock = $this->createMock(CampusSearch::class);
        $searchMock->expects(self::once())
            ->method('value')
            ->willReturn($data['search']);
        $this->campus->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $createdAt = $this->createMock(CampusCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['createdAt']);
        $this->campus->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(CampusUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn($data['updatedAt']);
        $this->campus->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);
    }
}
