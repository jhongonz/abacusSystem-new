<?php

namespace Tests\Feature\App\Http\Requests\Institution;

use App\Http\Requests\Institution\StoreInstitutionRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(StoreInstitutionRequest::class)]
class StoreInstitutionRequestTest extends TestCase
{
    private StoreInstitutionRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreInstitutionRequest();
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
            'institutionId' => ['nullable'],
            'code' => ['nullable'],
            'name' => ['required'],
            'shortname' => ['required'],
            'address' => ['required'],
            'phone' => ['required'],
            'email' => ['required', 'email:rfc'],
            'observations' => ['nullable'],
            'token' => ['nullable'],
        ];

        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(9, $result);
        $this->assertEquals($expected, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $expected = [
            'name.required' => 'El campo name es requerido',
            'shortname.required' => 'El campo shortname es requerido',
            'email.required' => 'El campo email es requerido',
            'email.email' => 'El campo email debe ser una dirección email valida',
            'phone.required' => 'El campo phone es requerido',
        ];

        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(5, $result);
        $this->assertEquals($expected, $result);
    }
}
