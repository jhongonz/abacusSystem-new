<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployees;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeesRequest;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employees;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchEmployees::class)]
#[CoversClass(UseCasesService::class)]
class SearchEmployeesTest extends TestCase
{
    private SearchEmployeesRequest|MockObject $request;

    private EmployeeRepositoryContract|MockObject $repository;

    private SearchEmployees $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(EmployeeRepositoryContract::class);
        $this->request = $this->createMock(SearchEmployeesRequest::class);
        $this->useCase = new SearchEmployees($this->repository);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->repository,
            $this->useCase
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnValueObject(): void
    {
        $employeesMock = $this->createMock(Employees::class);

        $this->request->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $this->repository->expects(self::once())
            ->method('getAll')
            ->with([])
            ->willReturn($employeesMock);

        $result = $this->useCase->execute($this->request);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $employeesMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnNull(): void
    {
        $this->request->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $this->repository->expects(self::once())
            ->method('getAll')
            ->with([])
            ->willReturn(null);

        $result = $this->useCase->execute($this->request);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testExecuteShouldReturnException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $requestMock = $this->createMock(DeleteEmployeeRequest::class);
        $this->useCase->execute($requestMock);
    }
}
