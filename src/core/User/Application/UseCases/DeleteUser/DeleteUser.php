<?php

namespace Core\User\Application\UseCases\DeleteUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Exception;

class DeleteUser extends UseCasesService
{
    public function __construct(UserRepositoryContract $userRepository)
    {
        parent::__construct($userRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|User
    {
        $this->validateRequest($request, DeleteUserRequest::class);
        $this->userRepository->delete($request->userId());

        return null;
    }
}
