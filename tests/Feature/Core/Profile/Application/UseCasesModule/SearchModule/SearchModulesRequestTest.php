<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\SearchModule\SearchModulesRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SearchModulesRequest::class)]
class SearchModulesRequestTest extends TestCase
{
    private SearchModulesRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new SearchModulesRequest();
    }

    public function tearDown(): void
    {
        unset($this->request);
        parent::tearDown();
    }

    public function testFiltersShouldReturnArray(): void
    {
        $result = $this->request->filters();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
