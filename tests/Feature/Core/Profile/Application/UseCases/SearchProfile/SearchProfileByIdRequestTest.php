<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\SearchProfile\SearchProfileByIdRequest;
use Core\Profile\Domain\ValueObjects\ProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchProfileByIdRequest::class)]
class SearchProfileByIdRequestTest extends TestCase
{
    private ProfileId|MockObject $profileId;
    private SearchProfileByIdRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileId = $this->createMock(ProfileId::class);
        $this->request = new SearchProfileByIdRequest($this->profileId);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->profileId
        );
        parent::tearDown();
    }

    public function test_profileId_should_return_object(): void
    {
        $result = $this->request->profileId();

        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame($result, $this->profileId);
    }
}
