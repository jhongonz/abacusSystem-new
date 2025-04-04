<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\UpdateProfile\UpdateProfileRequest;
use Core\Profile\Domain\ValueObjects\ProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateProfileRequest::class)]
class UpdateProfileRequestTest extends TestCase
{
    private ProfileId|MockObject $profileId;

    /** @var array<string, mixed> */
    private array $data = [];
    private UpdateProfileRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileId = $this->createMock(ProfileId::class);
        $this->request = new UpdateProfileRequest(
            $this->profileId,
            $this->data
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->data,
            $this->profileId
        );
        parent::tearDown();
    }

    public function testProfileIdShouldReturnObject(): void
    {
        $result = $this->request->profileId();

        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame($this->profileId, $result);
    }

    public function testDataShouldReturnArray(): void
    {
        $result = $this->request->data();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
