<?php

namespace Core\Profile\Application\UseCasesModule;

use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    protected ModuleRepositoryContract $moduleRepository;

    public function __construct(
        ModuleRepositoryContract $moduleRepository,
    ) {
        $this->moduleRepository = $moduleRepository;
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
