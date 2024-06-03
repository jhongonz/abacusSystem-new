<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Management;

use Core\User\Application\UseCases\CreateUser\CreateUser;
use Core\User\Application\UseCases\CreateUser\CreateUserRequest;
use Core\User\Application\UseCases\DeleteUser\DeleteUser;
use Core\User\Application\UseCases\DeleteUser\DeleteUserRequest;
use Core\User\Application\UseCases\SearchUser\SearchUserById;
use Core\User\Application\UseCases\SearchUser\SearchUserByIdRequest;
use Core\User\Application\UseCases\SearchUser\SearchUserByLogin;
use Core\User\Application\UseCases\SearchUser\SearchUserByLoginRequest;
use Core\User\Application\UseCases\UpdateUser\UpdateUser;
use Core\User\Application\UseCases\UpdateUser\UpdateUserRequest;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Infrastructure\Management\UserService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UserService::class)]
class UserServiceTest extends TestCase
{
    private UserFactoryContract|MockObject $factory;
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
        $this->factory = $this->createMock(UserFactoryContract::class);
        $this->searchUserById = $this->createMock(SearchUserById::class);
        $this->searchUserByLogin = $this->createMock(SearchUserByLogin::class);
        $this->updateUser = $this->createMock(UpdateUser::class);
        $this->createUser = $this->createMock(CreateUser::class);
        $this->deleteUser = $this->createMock(DeleteUser::class);

        $this->userService = new UserService(
            $this->factory,
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
            $this->factory,
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
        $this->factory->expects(self::once())
            ->method('buildLogin')
            ->with('login')
            ->willReturn($loginMock);

        $request = new SearchUserByLoginRequest($loginMock);

        $userMock = $this->createMock(User::class);

        $this->searchUserByLogin->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($userMock);

        $result = $this->userService->searchUserByLogin('login');

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
        $this->factory->expects(self::once())
            ->method('buildLogin')
            ->with('login')
            ->willReturn($loginMock);

        $request = new SearchUserByLoginRequest($loginMock);

        $this->searchUserByLogin->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->userService->searchUserByLogin('login');

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_search_user_by_id_should_return_user(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $request = new SearchUserByIdRequest($userIdMock);

        $userMock = $this->createMock(User::class);
        $this->searchUserById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($userMock);

        $result = $this->userService->searchUserById($userIdMock);

        $this->assertSame($userMock, $result);
        $this->assertInstanceOf(User::class, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_search_user_by_id_should_return_null(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $request = new SearchUserByIdRequest($userIdMock);

        $this->searchUserById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->userService->searchUserById($userIdMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_update_user_should_void(): void
    {
        $dataUpdate = [];
        $userIdMock = $this->createMock(UserId::class);
        $userMock = $this->createMock(User::class);

        $request = new UpdateUserRequest($userIdMock, $dataUpdate);

        $this->updateUser->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($userMock);

        $this->userService->updateUser($userIdMock, $dataUpdate);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_create_user_should_void(): void
    {
        $userMock = $this->createMock(User::class);
        $request = new CreateUserRequest($userMock);

        $this->createUser->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($userMock);

        $this->userService->createUser($userMock);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_delete_user_should_void(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $request = new DeleteUserRequest($userIdMock);

        $this->deleteUser->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $this->userService->deleteUser($userIdMock);
        $this->assertTrue(true);
    }
}
