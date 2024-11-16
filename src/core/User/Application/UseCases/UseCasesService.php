<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases;

use Core\User\Domain\Contracts\UserRepositoryContract;

abstract class UseCasesService implements ServiceContract
{
    public function __construct(
        protected readonly UserRepositoryContract $userRepository,
    ) {
    }

    /**
     * @throws \Exception
     */
    protected function validateRequest(RequestService $request, string $requestClass): RequestService
    {
        if (!$request instanceof $requestClass) {
            throw new \Exception('Request not valid');
        }

        return $request;
    }
}
