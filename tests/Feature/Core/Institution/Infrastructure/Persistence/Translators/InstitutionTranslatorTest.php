<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Translators;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionPhone;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution as InstitutionModel;
use Core\Institution\Infrastructure\Persistence\Translators\InstitutionTranslator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(InstitutionTranslator::class)]
class InstitutionTranslatorTest extends TestCase
{
    private InstitutionFactoryContract|MockObject $factory;
    private InstitutionModel|MockObject $model;
    private InstitutionTranslator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(InstitutionFactoryContract::class);
        $this->model = $this->createMock(InstitutionModel::class);
        $this->translator = new InstitutionTranslator($this->factory);
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->translator,
            $this->factory
        );
        parent::tearDown();
    }

    public function tet_setModel_should_return_self(): void
    {
        $result = $this->translator->setModel($this->model);
        $this->assertInstanceOf(InstitutionTranslator::class, $result);
        $this->assertSame($this->translator, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testToDomainShouldReturnObject(): void
    {
        $institutionMock = $this->createMock(Institution::class);

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);
        $idMock = $this->createMock(InstitutionId::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionId')
            ->with(1)
            ->willReturn($idMock);

        $this->model->expects(self::once())
            ->method('name')
            ->willReturn('name');
        $nameMock = $this->createMock(InstitutionName::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionName')
            ->with('name')
            ->willReturn($nameMock);

        $this->model->expects(self::once())
            ->method('phone')
            ->willReturn('phone');
        $phoneMock = $this->createMock(InstitutionPhone::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionPhone')
            ->with('phone')
            ->willReturn($phoneMock);

        $this->factory->expects(self::once())
            ->method('buildInstitution')
            ->with($idMock, $nameMock)
            ->willReturn($institutionMock);

        $this->translator->setModel($this->model);
        $result = $this->translator->toDomain();

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    public function testSetCollectionShouldReturnSelf(): void
    {
        $result = $this->translator->setCollection([]);

        $this->assertInstanceOf(InstitutionTranslator::class, $result);
        $this->assertSame($this->translator, $result);
    }

    public function testToDomainCollectionShouldReturnObject(): void
    {
        $this->translator->setCollection([1, 2, 3]);
        $result = $this->translator->toDomainCollection();

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertCount(3, $result->aggregator());
        $this->assertSame([1, 2, 3], $result->aggregator());
    }
}
