<?php

namespace Tests\Feature\Core\Institution\Application\DataTransformer;

use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use Core\SharedContext\Model\dateTimeModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
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
        $this->dataTransformer = new InstitutionDataTransformer;
    }

    public function tearDown(): void
    {
        unset(
            $this->dataTransformer,
            $this->institution
        );
        parent::tearDown();
    }

    public function test_write_should_return_self(): void
    {
        $result = $this->dataTransformer->write($this->institution);

        $this->assertInstanceOf(InstitutionDataTransformer::class, $result);
        $this->assertSame($this->dataTransformer, $result);
    }

    /**
     * @throws Exception
     */
    public function test_read_should_return_array(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->institution->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn('code');
        $this->institution->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $this->institution->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $shortname = $this->createMock(InstitutionShortname::class);
        $shortname->expects(self::once())
            ->method('value')
            ->willReturn('shortname');
        $this->institution->expects(self::once())
            ->method('shortname')
            ->willReturn($shortname);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn('logo');
        $this->institution->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('testing');
        $this->institution->expects(self::once())
            ->method('observations')
            ->willReturn($observations);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->institution->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $createdAt = $this->createMock(InstitutionCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn((new \DateTime)->format(dateTimeModel::DATE_FORMAT));
        $this->institution->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(InstitutionUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn((new \DateTime)->format(dateTimeModel::DATE_FORMAT));
        $this->institution->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->dataTransformer->write($this->institution);
        $result = $this->dataTransformer->read();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(Institution::TYPE, $result);
    }

    /**
     * @throws Exception
     */
    public function test_readToShare_should_return_array(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->institution->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('value')
            ->willReturn('code');
        $this->institution->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('value')
            ->willReturn('name');
        $this->institution->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $shortname = $this->createMock(InstitutionShortname::class);
        $shortname->expects(self::once())
            ->method('value')
            ->willReturn('shortname');
        $this->institution->expects(self::once())
            ->method('shortname')
            ->willReturn($shortname);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('value')
            ->willReturn('logo');
        $this->institution->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $observations = $this->createMock(InstitutionObservations::class);
        $observations->expects(self::once())
            ->method('value')
            ->willReturn('testing');
        $this->institution->expects(self::once())
            ->method('observations')
            ->willReturn($observations);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $stateMock->expects(self::once())
            ->method('formatHtmlToState')
            ->willReturn('test');
        $this->institution->expects(self::exactly(2))
            ->method('state')
            ->willReturn($stateMock);

        $createdAt = $this->createMock(InstitutionCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn((new \DateTime)->format(dateTimeModel::DATE_FORMAT));
        $this->institution->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(InstitutionUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('toFormattedString')
            ->willReturn((new \DateTime)->format(dateTimeModel::DATE_FORMAT));
        $this->institution->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->dataTransformer->write($this->institution);
        $result = $this->dataTransformer->readToShare();

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey(Institution::TYPE, $result);
    }
}
