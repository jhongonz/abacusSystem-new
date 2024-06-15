<?php

namespace Tests\Feature\App\Http\Requests\Module;

use App\Http\Requests\Module\StoreModuleRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(StoreModuleRequest::class)]
class StoreModuleRequestTest extends TestCase
{
    private StoreModuleRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreModuleRequest;
    }

    public function tearDown(): void
    {
        unset($this->request);
        parent::tearDown();
    }

    public function test_authorize_should_return_true(): void
    {
        $result = $this->request->authorize();
        $this->assertTrue($result);
    }

    public function test_rules_should_return_array(): void
    {
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
    }
    public function test_messages_should_return_array(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
    }
}
