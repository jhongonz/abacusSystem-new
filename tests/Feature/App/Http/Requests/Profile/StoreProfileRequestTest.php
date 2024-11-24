<?php

namespace Tests\Feature\App\Http\Requests\Profile;

use App\Http\Requests\Profile\StoreProfileRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(StoreProfileRequest::class)]
class StoreProfileRequestTest extends TestCase
{
    private StoreProfileRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreProfileRequest();
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
            'id' => ['nullable', 'numeric'],
            'name' => ['required'],
            'modules' => ['required', 'array'],
            'description' => ['nullable', 'string'],
        ];

        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertEquals($expected, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $expected = [
            'id.nullable' => 'El campo id, puede ser null o numerico',
            'id.numeric' => 'El campo id, puede ser null o numerico',
            'name.required' => 'El campo nombre es requerido',
            'modules.required' => 'El campo modules es requerido',
            'modules.array' => 'El campo modules debe ser un array',
            'description.string' => 'El campo description pueder ser texto o nulo',
            'description.nullable' => 'El campo description pueder ser texto o nulo',
        ];

        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(7, $result);
        $this->assertEquals($expected, $result);
    }
}
