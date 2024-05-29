<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\DeleteInstitution;

use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitutionRequest;
use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitution;
use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitutionRequest;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteInstitution::class)]
class DeleteInstitutionTest extends TestCase
{
    private InstitutionRepositoryContract|MockObject $repository;
    private DeleteInstitution $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(InstitutionRepositoryContract::class);
        $this->useCase = new DeleteInstitution($this->repository);
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
    public function test_execute_should_return_null(): void
    {
        $institutionMock = $this->createMock(InstitutionId::class);

        $requestMock = $this->createMock(DeleteInstitutionRequest::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($institutionMock);

        $this->repository->expects(self::once())
            ->method('delete')
            ->with($institutionMock);

        $result = $this->useCase->execute($requestMock);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(CreateInstitutionRequest::class);

        $this->repository->expects(self::never())
            ->method('delete');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
