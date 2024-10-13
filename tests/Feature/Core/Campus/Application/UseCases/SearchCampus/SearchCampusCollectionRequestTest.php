<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollectionRequest;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchCampusCollectionRequest::class)]
class SearchCampusCollectionRequestTest extends TestCase
{
    private CampusInstitutionId|MockObject $institutionId;
    private SearchCampusCollectionRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionId = $this->createMock(CampusInstitutionId::class);
        $this->request = new SearchCampusCollectionRequest($this->institutionId);
    }

    public function tearDown(): void
    {
        unset(
            $this->institutionId,
            $this->request
        );
        parent::tearDown();
    }

    public function test_institutionId_should_return_object(): void
    {
        $result = $this->request->institutionId();

        $this->assertInstanceOf(CampusInstitutionId::class, $result);
        $this->assertSame($this->institutionId, $result);
    }

    public function test_filters_should_return_array(): void
    {
        $result = $this->request->filters();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
