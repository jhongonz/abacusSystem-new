<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\CreateEmployee;

use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployeeRequest;
use Core\Employee\Domain\Employee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateEmployeeRequest::class)]
class CreateEmployeeRequestTest extends TestCase
{
    private Employee|MockObject $employee;
    private CreateEmployeeRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employee = $this->createMock(Employee::class);
        $this->request = new CreateEmployeeRequest($this->employee);
    }

    public function tearDown(): void
    {
        unset(
            $this->employee,
            $this->request
        );
        parent::tearDown();
    }

    public function test_employee_should_return_object(): void
    {
        $result = $this->request->employee();

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $this->employee);
    }
}
