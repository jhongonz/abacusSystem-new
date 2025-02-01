<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutions;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionsRequest;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institutions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchInstitutions::class)]
#[CoversClass(UseCasesService::class)]
class SearchInstitutionsTest extends TestCase
{
    private InstitutionRepositoryContract|MockObject $repository;
    private SearchInstitutions $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(InstitutionRepositoryContract::class);
        $this->useCase = new SearchInstitutions($this->repository);
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
    public function testExecuteShouldReturnObject(): void
    {
        $request = $this->createMock(SearchInstitutionsRequest::class);
        $request->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $institutionsMock = $this->createMock(Institutions::class);
        $this->repository->expects(self::once())
            ->method('getAll')
            ->with([])
            ->willReturn($institutionsMock);

        $result = $this->useCase->execute($request);

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($institutionsMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnNull(): void
    {
        $request = $this->createMock(SearchInstitutionsRequest::class);
        $request->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $this->repository->expects(self::once())
            ->method('getAll')
            ->with([])
            ->willReturn(null);

        $result = $this->useCase->execute($request);

        $this->assertNotInstanceOf(Institutions::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnException(): void
    {
        $request = $this->createMock(SearchInstitutionByIdRequest::class);

        $this->repository->expects(self::never())
            ->method('getAll');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($request);
    }
}
