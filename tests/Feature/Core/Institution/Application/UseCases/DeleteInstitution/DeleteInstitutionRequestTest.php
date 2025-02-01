<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\DeleteInstitution;

use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitutionRequest;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteInstitutionRequest::class)]
class DeleteInstitutionRequestTest extends TestCase
{
    private InstitutionId|MockObject $id;
    private DeleteInstitutionRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->id = $this->createMock(InstitutionId::class);
        $this->request = new DeleteInstitutionRequest($this->id);
    }

    public function tearDown(): void
    {
        unset($this->id, $this->request);
        parent::tearDown();
    }

    public function testIdShouldReturnObject(): void
    {
        $result = $this->request->id();

        $this->assertInstanceOf(InstitutionId::class, $result);
        $this->assertSame($this->id, $result);
    }
}
