<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\UpdateInstitution;

use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitutionRequest;
use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitution;
use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitutionRequest;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Institution\Application\UseCases\UpdateInstitution\DataProvider\DataProviderUpdateInstitution;
use Tests\TestCase;

#[CoversClass(UpdateInstitution::class)]
#[CoversClass(UseCasesService::class)]
class UpdateInstitutionTest extends TestCase
{
    private InstitutionRepositoryContract|MockObject $repository;
    private UpdateInstitution $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(InstitutionRepositoryContract::class);
        $this->useCase = new UpdateInstitution($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderUpdateInstitution::class, 'provider')]
    public function testExecuteShouldUpdateAndReturnObject(array $data): void
    {
        $requestMock = $this->createMock(UpdateInstitutionRequest::class);

        $idMock = $this->createMock(InstitutionId::class);

        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $requestMock->expects(self::once())
            ->method('data')
            ->willReturn($data);

        $institutionMock = $this->createMock(Institution::class);

        $codeMock = $this->createMock(InstitutionCode::class);
        $codeMock->expects(self::once())
            ->method('setValue')
            ->with('code')
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('code')
            ->willReturn($codeMock);

        $nameMock = $this->createMock(InstitutionName::class);
        $nameMock->expects(self::once())
            ->method('setValue')
            ->with('name')
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $shortnameMock = $this->createMock(InstitutionShortname::class);
        $shortnameMock->expects(self::once())
            ->method('setValue')
            ->with('shortname')
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('shortname')
            ->willReturn($shortnameMock);

        $logoMock = $this->createMock(InstitutionLogo::class);
        $logoMock->expects(self::once())
            ->method('setValue')
            ->with('logo')
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('logo')
            ->willReturn($logoMock);

        $observationsMock = $this->createMock(InstitutionObservations::class);
        $observationsMock->expects(self::once())
            ->method('setValue')
            ->with('observations')
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('observations')
            ->willReturn($observationsMock);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $updatedAtMock = $this->createMock(InstitutionUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with($data['updatedAt'])
            ->willReturnSelf();
        $institutionMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $institutionMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $this->repository->expects(self::once())
            ->method('find')
            ->with($idMock)
            ->willReturn($institutionMock);

        $this->repository->expects(self::once())
            ->method('persistInstitution')
            ->with($institutionMock)
            ->willReturn($institutionMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnException(): void
    {
        $requestMock = $this->createMock(CreateInstitutionRequest::class);

        $this->repository->expects(self::never())
            ->method('find');

        $this->repository->expects(self::never())
            ->method('persistInstitution');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
