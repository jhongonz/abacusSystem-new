<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployeeRequest;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateEmployeeRequest::class)]
class UpdateEmployeeRequestTest extends TestCase
{
    private EmployeeId|MockObject $employeeId;

    private UpdateEmployeeRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeId = $this->createMock(EmployeeId::class);
        $this->request = new UpdateEmployeeRequest($this->employeeId, []);
    }

    public function tearDown(): void
    {
        unset($this->employeeId, $this->request);
        parent::tearDown();
    }

    public function testEmployeeIdShouldReturnValueObject(): void
    {
        $result = $this->request->employeeId();

        $this->assertInstanceOf(EmployeeId::class, $result);
        $this->assertSame($result, $this->employeeId);
    }

    public function testDataShouldReturnArray(): void
    {
        $result = $this->request->data();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
