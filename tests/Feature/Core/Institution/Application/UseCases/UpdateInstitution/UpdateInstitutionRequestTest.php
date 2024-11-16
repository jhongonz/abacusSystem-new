<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\UpdateInstitution;

use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitutionRequest;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateInstitutionRequest::class)]
class UpdateInstitutionRequestTest extends TestCase
{
    private InstitutionId|MockObject $id;
    private UpdateInstitutionRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->id = $this->createMock(InstitutionId::class);
        $this->request = new UpdateInstitutionRequest($this->id, []);
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

    public function testDataShouldReturnArray(): void
    {
        $result = $this->request->data();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
