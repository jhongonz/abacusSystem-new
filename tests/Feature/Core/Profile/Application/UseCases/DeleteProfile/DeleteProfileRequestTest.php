<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\DeleteProfile;

use Core\Profile\Application\UseCases\DeleteProfile\DeleteProfileRequest;
use Core\Profile\Domain\ValueObjects\ProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteProfileRequest::class)]
class DeleteProfileRequestTest extends TestCase
{
    private ProfileId|MockObject $profileId;
    private DeleteProfileRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileId = $this->createMock(ProfileId::class);
        $this->request = new DeleteProfileRequest($this->profileId);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->profileId
        );
        parent::tearDown();
    }

    public function test_id_should_return_value_object(): void
    {
        $result = $this->request->id();
        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame($this->profileId, $result);
    }
}
