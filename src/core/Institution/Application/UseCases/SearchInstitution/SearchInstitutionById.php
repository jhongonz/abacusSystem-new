<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 12:46:55
 */

namespace Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Exception;

class SearchInstitutionById extends UseCasesService
{
    public function __construct(InstitutionRepositoryContract $institutionRepository)
    {
        parent::__construct($institutionRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|Institution
    {
        $this->validateRequest($request, SearchInstitutionByIdRequest::class);

        /** @var SearchInstitutionByIdRequest $request */
        return $this->institutionRepository->find($request->institutionId());
    }
}
