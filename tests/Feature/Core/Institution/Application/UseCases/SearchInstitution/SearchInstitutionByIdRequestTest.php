<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchInstitutionByIdRequest::class)]
class SearchInstitutionByIdRequestTest extends TestCase
{
    private InstitutionId|MockObject $id;
    private SearchInstitutionByIdRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->id = $this->createMock(InstitutionId::class);
        $this->request = new SearchInstitutionByIdRequest($this->id);
    }

    public function tearDown(): void
    {
        unset($this->id, $this->request);
        parent::tearDown();
    }

    public function testIdShouldReturnValueObject(): void
    {
        $result = $this->request->institutionId();

        $this->assertInstanceOf(InstitutionId::class, $result);
        $this->assertSame($this->id, $result);
    }
}
