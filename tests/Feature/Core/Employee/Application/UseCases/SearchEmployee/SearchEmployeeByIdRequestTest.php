<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdRequest;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchEmployeeByIdRequest::class)]
class SearchEmployeeByIdRequestTest extends TestCase
{
    private EmployeeId|MockObject $employeeId;

    private SearchEmployeeByIdRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeId = $this->createMock(EmployeeId::class);
        $this->request = new SearchEmployeeByIdRequest($this->employeeId);
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
}
