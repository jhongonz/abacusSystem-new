<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Management;

use Core\User\Application\UseCases\CreateUser\CreateUser;
use Core\User\Application\UseCases\DeleteUser\DeleteUser;
use Core\User\Application\UseCases\SearchUser\SearchUserById;
use Core\User\Application\UseCases\SearchUser\SearchUserByLogin;
use Core\User\Application\UseCases\SearchUser\SearchUserByLoginRequest;
use Core\User\Application\UseCases\UpdateUser\UpdateUser;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Infrastructure\Management\UserService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UserService::class)]
class UserServiceTest extends TestCase
{
    private SearchUserByLogin|MockObject $searchUserByLogin;
    private SearchUserById|MockObject $searchUserById;
    private UpdateUser|MockObject $updateUser;
    private CreateUser|MockObject $createUser;
    private DeleteUser|MockObject $deleteUser;
    private UserService $userService;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->searchUserById = $this->createMock(SearchUserById::class);
        $this->searchUserByLogin = $this->createMock(SearchUserByLogin::class);
        $this->updateUser = $this->createMock(UpdateUser::class);
        $this->createUser = $this->createMock(CreateUser::class);
        $this->deleteUser = $this->createMock(DeleteUser::class);

        $this->userService = new UserService(
            $this->searchUserByLogin,
            $this->searchUserById,
            $this->updateUser,
            $this->createUser,
            $this->deleteUser
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->searchUserByLogin,
            $this->searchUserById,
            $this->updateUser,
            $this->deleteUser,
            $this->createUser,
            $this->userService,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_search_user_by_login_should_return_user(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $request = new SearchUserByLoginRequest($loginMock);

        $userMock = $this->createMock(User::class);

        $this->searchUserByLogin->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($userMock);

        $result = $this->userService->searchUserByLogin($loginMock);

        $this->assertSame($userMock, $result);
        $this->assertInstanceOf(User::class, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_search_user_by_login_should_return_null(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $request = new SearchUserByLoginRequest($loginMock);

        $this->searchUserByLogin->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->userService->searchUserByLogin($loginMock);

        $this->assertNull($result);
    }
}
