<?php

namespace Core\Institution\Application\UseCases;

use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Exception;

/**
 * @codeCoverageIgnore
 */
abstract class UseCasesService implements ServiceContract
{
    protected InstitutionRepositoryContract $institutionRepository;

    public function __construct(
        InstitutionRepositoryContract $institutionRepository
    ) {
        $this->institutionRepository = $institutionRepository;
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
