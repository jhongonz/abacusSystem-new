<?php

namespace Tests\Feature\App\Http\Requests\User;

use App\Http\Requests\User\RecoveryAccountRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(RecoveryAccountRequest::class)]
class RecoveryAccountRequestTest extends TestCase
{
    private RecoveryAccountRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new RecoveryAccountRequest();
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
        $expected = [
            'identification' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
        ];

        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($expected, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $expected = [
            'identification.required' => 'El campo identification es requerido',
            'email.email' => 'El campo email debe ser una dirección valida',
            'email.required' => 'El campo email es requerido',
        ];

        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals($expected, $result);
    }
}
