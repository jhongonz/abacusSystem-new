<?php

namespace Tests\Feature\Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\SearchUser\SearchUserById;
use Core\User\Application\UseCases\SearchUser\SearchUserByIdRequest;
use Core\User\Application\UseCases\SearchUser\SearchUserByLoginRequest;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(SearchUserById::class)]
class SearchUserByIdTest extends TestCase
{
    private UserRepositoryContract|Mock $repository;
    private SearchUserById $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryContract::class);
        $this->useCase = new SearchUserById($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_user_object(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userMock = $this->createMock(User::class);

        $requestMock = $this->createMock(SearchUserByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($userIdMock)
            ->willReturn($userMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $userIdMock = $this->createMock(UserId::class);

        $requestMock = $this->createMock(SearchUserByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($userIdMock)
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);

        $this->assertNull($result);
        $this->assertSame(null, $result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_request_fail_should_return_exception(): void
    {
        $requestMock = $this->createMock(SearchUserByLoginRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
