<?php

namespace Tests\Feature\Core\Institution\Application\UseCases\CreateInstitution;

use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitutionRequest;
use Core\Institution\Domain\Institution;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateInstitutionRequest::class)]
class CreateInstitutionRequestTest extends TestCase
{
    private Institution|MockObject $institution;
    private CreateInstitutionRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institution = $this->createMock(Institution::class);
        $this->request = new CreateInstitutionRequest($this->institution);
    }

    public function tearDown(): void
    {
        unset($this->institution, $this->request);
        parent::tearDown();
    }

    public function test_institution_should_return_object(): void
    {
        $result = $this->request->institution();

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($this->institution, $result);
    }
}
