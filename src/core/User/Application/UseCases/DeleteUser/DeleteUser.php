<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\DeleteUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
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
    public function execute(RequestService $request): null
    {
        $this->validateRequest($request, DeleteUserRequest::class);

        /** @var DeleteUserRequest $request */
        $this->userRepository->delete($request->userId());

        return null;
    }
}
