<?php

namespace Core\Campus\Application\UseCases;

use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    public function __construct(
        protected readonly CampusRepositoryContract $campusRepository
    ) {
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
