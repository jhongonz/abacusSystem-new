<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\DeleteEmployee;

use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteEmployeeRequest::class)]
class DeleteEmployeeRequestTest extends TestCase
{
    private EmployeeId|MockObject $employeeId;
    private DeleteEmployeeRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeId = $this->createMock(EmployeeId::class);
        $this->request = new DeleteEmployeeRequest($this->employeeId);
    }

    public function tearDown(): void
    {
        unset(
            $this->employeeId,
            $this->request
        );
        parent::tearDown();
    }

    public function test_employeeId_should_return_value_object(): void
    {
        $result = $this->request->employeeId();

        $this->assertInstanceOf(EmployeeId::class, $result);
        $this->assertSame($result, $this->employeeId);
    }
}
