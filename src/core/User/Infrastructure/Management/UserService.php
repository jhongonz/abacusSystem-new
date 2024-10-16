<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Management;

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
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Exception;

class UserService implements UserManagementContract
{
    public function __construct(
        private readonly UserFactoryContract $userFactory,
        private readonly SearchUserByLogin $searchUserByLogin,
        private readonly SearchUserById $searchUserById,
        private readonly UpdateUser $updateUser,
        private readonly CreateUser $createUser,
        private readonly DeleteUser $deleteUser,
    ) {
    }

    /**
     * @throws Exception
     */
    public function searchUserByLogin(string $login): ?User
    {
        $request = new SearchUserByLoginRequest(
            $this->userFactory->buildLogin($login)
        );

        return $this->searchUserByLogin->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchUserById(?int $id): ?User
    {
        $request = new SearchUserByIdRequest(
            $this->userFactory->buildId($id)
        );

        return $this->searchUserById->execute($request);
    }

    /**
     * @throws Exception
     */
    public function updateUser(int $id, array $data): User
    {
        $userId = $this->userFactory->buildId($id);
        $request = new UpdateUserRequest($userId, $data);

        return $this->updateUser->execute($request);
    }

    /**
     * @throws Exception
     */
    public function createUser(array $data): User
    {
        $user = $this->userFactory->buildUserFromArray($data);
        $request = new CreateUserRequest($user);

        return $this->createUser->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteUser(int $id): void
    {
        $request = new DeleteUserRequest(
            $this->userFactory->buildId($id)
        );

        $this->deleteUser->execute($request);
    }
}
