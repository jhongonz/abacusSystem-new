<?php

namespace Core\Profile\Application\UseCases;

use Core\Profile\Domain\Contracts\ProfileRepositoryContract;

abstract class UseCasesService implements ServiceContract
{
    public function __construct(
        protected readonly ProfileRepositoryContract $profileRepository,
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
