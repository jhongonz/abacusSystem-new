<?php

namespace Core\Campus\Application\UseCases;

use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Exception;

/**
 * @codeCoverageIgnore
 */
abstract class UseCasesService implements ServiceContract
{
    protected CampusRepositoryContract $campusRepository;

    public function __construct(
        CampusRepositoryContract $campusRepository
    ) {
        $this->campusRepository = $campusRepository;
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
