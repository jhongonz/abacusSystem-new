<?php

namespace Core\Institution\Application\UseCases;

use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    public function __construct(
        protected readonly InstitutionRepositoryContract $institutionRepository
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
