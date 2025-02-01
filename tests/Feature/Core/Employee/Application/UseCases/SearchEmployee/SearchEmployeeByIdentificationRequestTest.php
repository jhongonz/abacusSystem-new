<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentificationRequest;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchEmployeeByIdentificationRequest::class)]
class SearchEmployeeByIdentificationRequestTest extends TestCase
{
    private EmployeeIdentification|MockObject $identification;

    private SearchEmployeeByIdentificationRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->identification = $this->createMock(EmployeeIdentification::class);
        $this->request = new SearchEmployeeByIdentificationRequest($this->identification);
    }

    public function tearDown(): void
    {
        unset($this->identification, $this->request);
        parent::tearDown();
    }

    public function testEmployeeIdShouldReturnValueObject(): void
    {
        $result = $this->request->employeeIdentification();

        $this->assertInstanceOf(EmployeeIdentification::class, $result);
        $this->assertSame($result, $this->identification);
    }
}
