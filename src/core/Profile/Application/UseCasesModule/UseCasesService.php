<?php

namespace Core\Profile\Application\UseCasesModule;

use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    public function __construct(
        protected readonly ModuleRepositoryContract $moduleRepository,
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
