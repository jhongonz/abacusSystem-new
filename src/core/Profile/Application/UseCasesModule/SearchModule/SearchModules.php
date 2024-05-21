<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Modules;
use Exception;

class SearchModules extends UseCasesService
{
    public function __construct(ModuleRepositoryContract $moduleRepository)
    {
        parent::__construct($moduleRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): ?Modules
    {
        $this->validateRequest($request, SearchModulesRequest::class);

        /** @var SearchModulesRequest $request */
        return $this->moduleRepository->getAll($request->filters());
    }
}
