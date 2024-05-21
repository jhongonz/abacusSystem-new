<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 12:56:37
 */

namespace Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Exception;

class SearchInstitutions extends UseCasesService
{
    public function __construct(InstitutionRepositoryContract $institutionRepository)
    {
        parent::__construct($institutionRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|Institutions
    {
        $this->validateRequest($request, SearchInstitutionsRequest::class);

        return $this->institutionRepository->getAll($request->filters());
    }
}
