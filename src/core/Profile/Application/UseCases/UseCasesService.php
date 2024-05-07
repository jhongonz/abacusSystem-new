<?php

namespace Core\Profile\Application\UseCases;

use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    protected ProfileRepositoryContract $profileRepository;

    public function __construct(
        ProfileRepositoryContract $profileRepository,
    ) {
        $this->profileRepository = $profileRepository;
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
