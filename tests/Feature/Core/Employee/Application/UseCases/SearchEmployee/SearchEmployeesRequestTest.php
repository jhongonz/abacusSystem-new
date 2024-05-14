<?php

namespace Tests\Feature\Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeesRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SearchEmployeesRequest::class)]
class SearchEmployeesRequestTest extends TestCase
{
    private SearchEmployeesRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new SearchEmployeesRequest;
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
    }
}
