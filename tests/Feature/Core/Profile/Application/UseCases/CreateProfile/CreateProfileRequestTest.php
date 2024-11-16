<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\CreateProfile;

use Core\Profile\Application\UseCases\CreateProfile\CreateProfileRequest;
use Core\Profile\Domain\Profile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateProfileRequest::class)]
class CreateProfileRequestTest extends TestCase
{
    private Profile|MockObject $profile;

    private CreateProfileRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profile = $this->createMock(Profile::class);
        $this->request = new CreateProfileRequest($this->profile);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->profile
        );
        parent::tearDown();
    }

    public function testProfileShouldReturnObject(): void
    {
        $result = $this->request->profile();

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->profile, $result);
    }
}
