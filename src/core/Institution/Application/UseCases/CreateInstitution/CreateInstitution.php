<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 22:18:57
 */

namespace Core\Institution\Application\UseCases\CreateInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;

class CreateInstitution extends UseCasesService
{
    public function __construct(InstitutionRepositoryContract $institutionRepository)
    {
        parent::__construct($institutionRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): Institution
    {
        $this->validateRequest($request, CreateInstitutionRequest::class);

        /** @var CreateInstitutionRequest $request */
        $institution = $request->institution();
        $institution->refreshSearch();

        /* @var CreateInstitutionRequest $request */
        return $this->institutionRepository->persistInstitution($institution);
    }
}
