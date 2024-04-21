<?php

namespace Tests\Feature\Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\SearchUser\SearchUserByIdRequest;
use Core\User\Domain\ValueObjects\UserId;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(SearchUserByIdRequest::class)]
class SearchUserByIdRequestTest extends TestCase
{
    private UserId|Mock $userId;
    private SearchUserByIdRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userId = $this->createMock(UserId::class);
        $this->request = new SearchUserByIdRequest($this->userId);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->userId
        );
        parent::tearDown();
    }

    public function test_userId_should_return_value_object(): void
    {
        $result = $this->request->userId();

        $this->assertInstanceOf(UserId::class, $result);
        $this->assertSame($this->userId, $result);
    }
}
