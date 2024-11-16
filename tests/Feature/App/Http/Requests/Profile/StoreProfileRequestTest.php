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
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(7, $result);
    }
}
