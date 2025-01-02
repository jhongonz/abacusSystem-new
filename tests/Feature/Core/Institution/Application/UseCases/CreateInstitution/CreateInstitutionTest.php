<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\CreateInstitution;

use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitution;
use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitutionRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateInstitution::class)]
#[CoversClass(UseCasesService::class)]
class CreateInstitutionTest extends TestCase
{
    private InstitutionRepositoryContract|MockObject $repository;
    private CreateInstitution $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(InstitutionRepositoryContract::class);
        $this->useCase = new CreateInstitution($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldSaveAndReturnObject(): void
    {
        $institutionMock = $this->createMock(Institution::class);
        $institutionMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $requestMock = $this->createMock(CreateInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('institution')
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
        $requestMock = $this->createMock(SearchInstitutionByIdRequest::class);

        $this->repository->expects(self::never())
            ->method('persistInstitution');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
