<?php

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
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Exception;

class UserService implements UserManagementContract
{
    private SearchUserByLogin $searchUserByLogin;
    private SearchUserById $searchUserById;
    private UpdateUser $updateUser;
    private CreateUser $createUser;
    private DeleteUser $deleteUser;

    public function __construct(
        SearchUserByLogin $searchUserByLogin,
        SearchUserById $searchUserById,
        UpdateUser $updateUser,
        CreateUser $createUser,
        DeleteUser $deleteUser,
    ) {
        $this->searchUserByLogin = $searchUserByLogin;
        $this->searchUserById = $searchUserById;
        $this->updateUser = $updateUser;
        $this->createUser = $createUser;
        $this->deleteUser = $deleteUser;
    }

    /**
     * @throws Exception
     */
    public function searchUserByLogin(UserLogin $login): null|User
    {
        $request = new SearchUserByLoginRequest($login);

        return $this->searchUserByLogin->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchUserById(UserId $id): null|User
    {
        $request = new SearchUserByIdRequest($id);

        return $this->searchUserById->execute($request);
    }

    /**
     * @throws Exception
     */
    public function updateUser(UserId $id, array $data): void
    {
        $request = new UpdateUserRequest($id, $data);
        $this->updateUser->execute($request);
    }

    /**
     * @throws Exception
     */
    public function createUser(User $user): void
    {
        $request = new CreateUserRequest($user);
        $this->createUser->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteUser(UserId $id): void
    {
        $request = new DeleteUserRequest($id);
        $this->deleteUser->execute($request);
    }
}
