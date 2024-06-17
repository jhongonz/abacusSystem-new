<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:12:08
 */

namespace Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Exception;

class SearchCampusCollection extends UseCasesService
{
    public function __construct(CampusRepositoryContract $campusRepository)
    {
        parent::__construct($campusRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): ?CampusCollection
    {
        $this->validateRequest($request, SearchCampusCollectionRequest::class);

        /** @var SearchCampusCollectionRequest $request */
        return $this->campusRepository->getAll($request->institutionId(), $request->filters());
    }
}
