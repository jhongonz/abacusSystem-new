<?php

namespace Tests\Feature\App\Http\Requests\Employee;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(StoreEmployeeRequest::class)]
class StoreEmployeeRequestTest extends TestCase
{
    private StoreEmployeeRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreEmployeeRequest();
    }

    public function tearDown(): void
    {
        unset($this->request);
        parent::tearDown();
    }

    public function testAuthorizeShouldReturnTrue(): void
    {
        $result = $this->request->authorize();
        $this->assertTrue($result);
    }

    public function testRulesShouldReturnArray(): void
    {
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(10, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(11, $result);
    }
}
