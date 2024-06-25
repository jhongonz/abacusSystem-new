<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\SearchCampus\SearchCampusByIdRequest;
use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchCampusByIdRequest::class)]
class SearchCampusByIdRequestTest extends TestCase
{
    private CampusId|MockObject $campusId;
    private SearchCampusByIdRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusId = $this->createMock(CampusId::class);
        $this->request = new SearchCampusByIdRequest($this->campusId);
    }

    public function tearDown(): void
    {
        unset(
            $this->campusId,
            $this->request
        );
        parent::tearDown();
    }

    public function test_id_should_return_object(): void
    {
        $result = $this->request->id();

        $this->assertInstanceOf(CampusId::class, $result);
        $this->assertSame($this->campusId, $result);
    }
}
