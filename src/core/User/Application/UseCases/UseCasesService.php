<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases;

use Core\User\Domain\Contracts\UserRepositoryContract;
use Exception;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

/**
 * @codeCoverageIgnore
 */
abstract class UseCasesService implements ServiceContract
{
    protected UserRepositoryContract $userRepository;

    public function __construct(
        UserRepositoryContract $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     */
    protected function validateRequest(RequestService $request, string $requestClass): RequestService
    {
        if (! $request instanceof $requestClass) {
            throw new Exception('Request not valid');
        }

        return $request;
    }
}
