<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 22:23:08
 */

namespace Core\Institution\Application\UseCases\DeleteInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;

class DeleteInstitution extends UseCasesService
{
    public function __construct(InstitutionRepositoryContract $institutionRepository)
    {
        parent::__construct($institutionRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): null
    {
        $this->validateRequest($request, DeleteInstitutionRequest::class);

        /** @var DeleteInstitutionRequest $request */
        $this->institutionRepository->delete($request->id());

        return null;
    }
}
