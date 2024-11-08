<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionById;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionsRequest;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchInstitutionById::class)]
#[CoversClass(UseCasesService::class)]
class SearchInstitutionByIdTest extends TestCase
{
    private InstitutionRepositoryContract|MockObject $repository;
    private SearchInstitutionById $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(InstitutionRepositoryContract::class);
        $this->useCase = new SearchInstitutionById($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_object(): void
    {
        $idMock = $this->createMock(InstitutionId::class);

        $requestMock = $this->createMock(SearchInstitutionByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($idMock);

        $institutionMock = $this->createMock(Institution::class);
        $this->repository->expects(self::once())
            ->method('find')
            ->with($idMock)
            ->willReturn($institutionMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Institution::class, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $idMock = $this->createMock(InstitutionId::class);

        $requestMock = $this->createMock(SearchInstitutionByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($idMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($idMock)
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);

        $this->assertNotInstanceOf(Institution::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(SearchInstitutionsRequest::class);

        $this->repository->expects(self::never())
            ->method('find');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
