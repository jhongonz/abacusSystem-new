<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:03:00
 */

namespace Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Exception;

class SearchCampusById extends UseCasesService
{
    public function __construct(CampusRepositoryContract $campusRepository)
    {
        parent::__construct($campusRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): ?Campus
    {
        $this->validateRequest($request, SearchCampusByIdRequest::class);

        return $this->campusRepository->find($request->id());
    }
}
