<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\SearchProfile\SearchProfilesRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SearchProfilesRequest::class)]
class SearchProfilesRequestTest extends TestCase
{
    private SearchProfilesRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new SearchProfilesRequest();
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

    public function testFiltersShouldReturnArrayWithInitialization(): void
    {
        $requestMock = new SearchProfilesRequest(['testing']);
        $result = $requestMock->filters();

        $this->assertIsArray($result);
        $this->assertSame(['testing'], $result);
    }
}
