<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeById;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdRequest;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchEmployeeById::class)]
#[CoversClass(UseCasesService::class)]
class SearchEmployeeByIdTest extends TestCase
{
    private SearchEmployeeByIdRequest|MockObject $request;

    private EmployeeRepositoryContract|MockObject $repository;

    private SearchEmployeeById $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(EmployeeRepositoryContract::class);
        $this->request = $this->createMock(SearchEmployeeByIdRequest::class);
        $this->useCase = new SearchEmployeeById($this->repository);
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
     * @throws \Exception
     * @throws Exception
     */
    public function testExecuteShouldReturnValueObject(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeMock = $this->createMock(Employee::class);

        $this->request->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeIdMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $result = $this->useCase->execute($this->request);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testExecuteShouldReturnNull(): void
    {
        $employeeIdMock = $this->createMock(EmployeeId::class);

        $this->request->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeIdMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($employeeIdMock)
            ->willReturn(null);

        $result = $this->useCase->execute($this->request);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $requestMock = $this->createMock(DeleteEmployeeRequest::class);
        $this->useCase->execute($requestMock);
    }
}
