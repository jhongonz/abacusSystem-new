<?php

namespace Tests\Feature\App\Http\Requests\User;

use App\Http\Requests\User\LoginRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(LoginRequest::class)]
class LoginRequestTest extends TestCase
{
    private LoginRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new LoginRequest();
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
        $this->assertCount(2, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }
}
