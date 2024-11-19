<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Persistence\Translators;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\ValueObjects\CampusAddress;
use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use Core\Campus\Domain\ValueObjects\CampusEmail;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Domain\ValueObjects\CampusName;
use Core\Campus\Domain\ValueObjects\CampusPhone;
use Core\Campus\Domain\ValueObjects\CampusUpdatedAt;
use Core\Campus\Infrastructure\Persistence\Eloquent\Model\Campus as CampusModel;
use Core\Campus\Infrastructure\Persistence\Translators\CampusTranslator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CampusTranslator::class)]
class CampusTranslatorTest extends TestCase
{
    private CampusFactoryContract|MockObject $campusFactoryMock;
    private CampusTranslator $campusTranslator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusFactoryMock = $this->createMock(CampusFactoryContract::class);
        $this->campusTranslator = new CampusTranslator($this->campusFactoryMock);
    }

    protected function tearDown(): void
    {
        unset(
            $this->campusFactoryMock,
            $this->campusTranslator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testSetModelShouldReturnSelf(): void
    {
        $modelMock = $this->createMock(CampusModel::class);
        $result = $this->campusTranslator->setModel($modelMock);

        $this->assertInstanceOf(CampusTranslator::class, $result);
        $this->assertSame($this->campusTranslator, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testToDomainShouldReturnCampusObject(): void
    {
        $modelMock = $this->createMock(CampusModel::class);

        $modelMock->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $modelMock->expects(self::once())
            ->method('institutionId')
            ->willReturn(2);

        $modelMock->expects(self::once())
            ->method('name')
            ->willReturn('testing');

        $modelMock->expects(self::once())
            ->method('address')
            ->willReturn('address');

        $modelMock->expects(self::once())
            ->method('email')
            ->willReturn('email');

        $modelMock->expects(self::once())
            ->method('phone')
            ->willReturn('phone');

        $datetime = new \DateTime();
        $modelMock->expects(self::exactly(2))
            ->method('createdAt')
            ->willReturn($datetime);

        $modelMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($datetime);

        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactoryMock->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturn($campusIdMock);

        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $this->campusFactoryMock->expects(self::once())
            ->method('buildCampusInstitutionId')
            ->with(2)
            ->willReturn($institutionIdMock);

        $nameMock = $this->createMock(CampusName::class);
        $this->campusFactoryMock->expects(self::once())
            ->method('buildCampusName')
            ->with('testing')
            ->willReturn($nameMock);

        $campusMock = $this->createMock(Campus::class);

        $addressMock = $this->createMock(CampusAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with('address')
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $emailMock = $this->createMock(CampusEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with('email')
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $phoneMock = $this->createMock(CampusPhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with('phone')
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $createdAtMock = $this->createMock(CampusCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('setValue')
            ->with($datetime)
            ->willReturnSelf();
        $campusMock->expects(self::exactly(2))
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(CampusUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with($datetime)
            ->willReturnSelf();
        $campusMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $this->campusFactoryMock->expects(self::once())
            ->method('buildCampus')
            ->with($campusIdMock, $institutionIdMock, $nameMock)
            ->willReturn($campusMock);

        $this->campusTranslator->setModel($modelMock);
        $result = $this->campusTranslator->toDomain();

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    public function testSetCollectionShouldReturnSelf(): void
    {
        $result = $this->campusTranslator->setCollection([1, 2, 3]);

        $this->assertInstanceOf(CampusTranslator::class, $result);
        $this->assertSame($this->campusTranslator, $result);
    }

    public function testToDomainCollectionShouldReturnObject(): void
    {
        $expected = [1, 2, 3];
        $this->campusTranslator->setCollection($expected);

        $result = $this->campusTranslator->toDomainCollection();

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertIsArray($result->aggregator());
        $this->assertSame($expected, $result->aggregator());
        $this->assertCount(3, $result->aggregator());
    }
}
