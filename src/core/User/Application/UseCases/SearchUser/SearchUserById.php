<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;

class SearchUserById extends UseCasesService
{
    public function __construct(UserRepositoryContract $userRepository)
    {
        parent::__construct($userRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): ?User
    {
        $this->validateRequest($request, SearchUserByIdRequest::class);

        /* @var SearchUserByIdRequest $request */
        return $this->userRepository->find($request->userId());
    }
}
