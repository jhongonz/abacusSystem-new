<?php

namespace Tests\Feature\App\Http\Requests\User;

use App\Http\Requests\User\ResetPasswordRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ResetPasswordRequest::class)]
class ResetPasswordRequestTest extends TestCase
{
    private ResetPasswordRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new ResetPasswordRequest();
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
        $this->assertCount(3, $result);

        foreach ($result as $index => $item) {
            $this->assertIsString($index);

            $this->assertIsArray($item);
            $this->assertNotEmpty($item);
        }
    }

    public function testMessagesShouldReturnArray(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
    }
}
