<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Translators;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
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
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution as InstitutionModel;
use Core\Institution\Infrastructure\Persistence\Translators\InstitutionTranslator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Institution\Infrastructure\Persistence\Translators\DataProvider\InstitutionTranslatorDataProvider;
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
     * @param array<string, mixed> $dataProvider
     *
     * @throws Exception
     */
    #[DataProviderExternal(InstitutionTranslatorDataProvider::class, 'provider')]
    public function testToDomainShouldReturnObject(array $dataProvider): void
    {
        $institutionMock = $this->createMock(Institution::class);

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn($dataProvider['id']);
        $idMock = $this->createMock(InstitutionId::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionId')
            ->with($dataProvider['id'])
            ->willReturn($idMock);

        $this->model->expects(self::once())
            ->method('name')
            ->willReturn($dataProvider['name']);
        $nameMock = $this->createMock(InstitutionName::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionName')
            ->with($dataProvider['name'])
            ->willReturn($nameMock);

        $this->model->expects(self::once())
            ->method('shortname')
            ->willReturn($dataProvider['shortname']);
        $shortnameMock = $this->createMock(InstitutionShortname::class);
        $shortnameMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['shortname'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('shortname')
            ->willReturn($shortnameMock);

        $this->model->expects(self::once())
            ->method('code')
            ->willreturn($dataProvider['code']);
        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['code'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $this->model->expects(self::once())
            ->method('logo')
            ->willReturn($dataProvider['logo']);
        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['logo'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $this->model->expects(self::once())
            ->method('state')
            ->willReturn($dataProvider['state']);
        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['state'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->model->expects(self::once())
            ->method('observations')
            ->willReturn($dataProvider['observations']);
        $observationsMock = $this->createMock(InstitutionObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['observations'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $this->model->expects(self::once())
            ->method('address')
            ->willReturn($dataProvider['address']);
        $addressMock = $this->createMock(InstitutionAddress::class);
        $addressMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['address'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('address')
            ->willReturn($addressMock);

        $this->model->expects(self::once())
            ->method('phone')
            ->willReturn($dataProvider['phone']);
        $phoneMock = $this->createMock(InstitutionPhone::class);
        $phoneMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['phone'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('phone')
            ->willReturn($phoneMock);

        $this->model->expects(self::once())
            ->method('email')
            ->willReturn($dataProvider['email']);
        $emailMock = $this->createMock(InstitutionEmail::class);
        $emailMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['email'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('email')
            ->willReturn($emailMock);

        $this->model->expects(self::once())
            ->method('search')
            ->willReturn($dataProvider['search']);
        $searchMock = $this->createMock(InstitutionSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['search'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('search')
            ->willReturn($searchMock);

        $this->model->expects(self::once())
            ->method('createdAt')
            ->willReturn($dataProvider['createdAt']);

        if (!is_null($dataProvider['createdAt'])) {
            $createdAtMock = $this->createMock(InstitutionCreatedAt::class);
            $createdAtMock->expects(self::once())
                ->method('setValue')
                ->with($dataProvider['createdAt'])
                ->willReturnSelf();
            $institutionMock->expects(self::once())
                ->method('createdAt')
                ->willReturn($createdAtMock);
        }

        $this->model->expects(self::once())
            ->method('updatedAt')
            ->willReturn($dataProvider['updatedAt']);

        if (!is_null($dataProvider['updatedAt'])) {
            $updatedAtMock = $this->createMock(InstitutionUpdatedAt::class);
            $updatedAtMock->expects(self::once())
                ->method('setValue')
                ->with($dataProvider['updatedAt'])
                ->willReturnSelf();
            $institutionMock->expects(self::once())
                ->method('updatedAt')
                ->willReturn($updatedAtMock);
        }

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
