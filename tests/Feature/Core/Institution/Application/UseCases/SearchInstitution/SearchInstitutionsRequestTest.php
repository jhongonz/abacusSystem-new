<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionsRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SearchInstitutionsRequest::class)]
class SearchInstitutionsRequestTest extends TestCase
{
    private SearchInstitutionsRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new SearchInstitutionsRequest([]);
    }

    public function tearDown(): void
    {
        unset($this->request);
        parent::tearDown();
    }

    public function test_filters_should_return_array(): void
    {
        $result = $this->request->filters();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
