<?php

namespace Tests\Feature\App\Http\Requests\Campus;

use App\Http\Requests\Campus\StoreCampusRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(StoreCampusRequest::class)]
class StoreCampusRequestTest extends TestCase
{
    private StoreCampusRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreCampusRequest();
    }

    protected function tearDown(): void
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
        $this->assertCount(6, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }
}
