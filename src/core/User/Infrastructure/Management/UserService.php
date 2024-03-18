<?php

namespace Core\User\Infrastructure\Management;

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
    
    public function __construct(
        SearchUserByLogin $searchUserByLogin,
        SearchUserById $searchUserById,
        UpdateUser $updateUser,
    ) {
        $this->searchUserByLogin = $searchUserByLogin;
        $this->searchUserById = $searchUserById;
        $this->updateUser = $updateUser;
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
    public function updateUser(UserId $id, array $data): User
    {
        $request = new UpdateUserRequest($id, $data);
        
        return $this->updateUser->execute($request);
    }
}