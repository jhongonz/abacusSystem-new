<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\CreateEmployee;

use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployee;
use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployeeRequest;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateEmployee::class)]
#[CoversClass(UseCasesService::class)]
class CreateEmployeeTest extends TestCase
{
    private CreateEmployeeRequest|MockObject $request;

    private EmployeeRepositoryContract|MockObject $repository;

    private CreateEmployee $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(CreateEmployeeRequest::class);
        $this->repository = $this->createMock(EmployeeRepositoryContract::class);
        $this->useCase = new CreateEmployee($this->repository);
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
    public function test_execute_should_persist_and_return_object_employee(): void
    {
        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $this->request->expects(self::once())
            ->method('employee')
            ->willReturn($employeeMock);

        $this->repository->expects(self::once())
            ->method('persistEmployee')
            ->with($employeeMock)
            ->willReturn($employeeMock);

        $result = $this->useCase->execute($this->request);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $requestMock = $this->createMock(DeleteEmployeeRequest::class);
        $this->useCase->execute($requestMock);

    }
}
