<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\DeleteEmployee;

use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployeeRequest;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployee;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteEmployee::class)]
#[CoversClass(UseCasesService::class)]
class DeleteEmployeeTest extends TestCase
{
    private EmployeeRepositoryContract|MockObject $repository;

    private DeleteEmployeeRequest|MockObject $request;

    private DeleteEmployee $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(EmployeeRepositoryContract::class);
        $this->request = $this->createMock(DeleteEmployeeRequest::class);
        $this->useCase = new DeleteEmployee($this->repository);
    }

    public function tearDown(): void
    {
        unset(
            $this->repository,
            $this->request,
            $this->useCase
        );
        parent::tearDown();
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function test_execute_should_delete_and_return_null(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $this->request->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeId);

        $this->repository->expects(self::once())
            ->method('delete')
            ->with($employeeId);

        $result = $this->useCase->execute($this->request);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $requestMock = $this->createMock(CreateEmployeeRequest::class);
        $this->useCase->execute($requestMock);
    }
}
