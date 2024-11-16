<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:57:34
 */

namespace Core\Campus\Application\UseCases\DeleteCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;

class DeleteCampus extends UseCasesService
{
    public function __construct(CampusRepositoryContract $campusRepository)
    {
        parent::__construct($campusRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): null
    {
        $this->validateRequest($request, DeleteCampusRequest::class);

        /* @var DeleteCampusRequest $request */
        $this->campusRepository->delete($request->id());

        return null;
    }
}
