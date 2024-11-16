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
        $result = $this->request->rules();

        $this->assertIsArray($result);
        $this->assertCount(9, $result);
    }

    public function testMessagesShouldReturnArray(): void
    {
        $result = $this->request->messages();

        $this->assertIsArray($result);
        $this->assertCount(5, $result);
    }
}
