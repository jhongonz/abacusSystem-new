<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:52:19
 */

namespace Core\Campus\Application\UseCases\CreateCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;

class CreateCampus extends UseCasesService
{
    public function __construct(CampusRepositoryContract $campusRepository)
    {
        parent::__construct($campusRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): Campus
    {
        $this->validateRequest($request, CreateCampusRequest::class);

        /** @var CreateCampusRequest $request */
        $campus = $request->campus();
        $campus->refreshSearch();

        return $this->campusRepository->persistCampus($campus);
    }
}
