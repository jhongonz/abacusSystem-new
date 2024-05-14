<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentification;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentificationRequest;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchEmployeeByIdentification::class)]
class SearchEmployeeByIdentificationTest extends TestCase
{
    private SearchEmployeeByIdentificationRequest|MockObject $request;

    private EmployeeRepositoryContract|MockObject $repository;

    private SearchEmployeeByIdentification $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(EmployeeRepositoryContract::class);
        $this->request = $this->createMock(SearchEmployeeByIdentificationRequest::class);
        $this->useCase = new SearchEmployeeByIdentification($this->repository);
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
    public function test_execute_should_return_value_object(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);
        $employeeMock = $this->createMock(Employee::class);

        $this->request->expects(self::once())
            ->method('employeeIdentification')
            ->willReturn($identificationMock);

        $this->repository->expects(self::once())
            ->method('findCriteria')
            ->with($identificationMock)
            ->willReturn($employeeMock);

        $result = $this->useCase->execute($this->request);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function test_execute_should_return_null(): void
    {
        $identificationMock = $this->createMock(EmployeeIdentification::class);

        $this->request->expects(self::once())
            ->method('employeeIdentification')
            ->willReturn($identificationMock);

        $this->repository->expects(self::once())
            ->method('findCriteria')
            ->with($identificationMock)
            ->willReturn(null);

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

        $requestMock = $this->createMock(DeleteEmployeeRequest::class);
        $this->useCase->execute($requestMock);
    }
}
