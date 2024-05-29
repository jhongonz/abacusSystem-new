<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Exception;

class SearchModuleById extends UseCasesService
{
    public function __construct(ModuleRepositoryContract $moduleRepository)
    {
        parent::__construct($moduleRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): ?Module
    {
        $this->validateRequest($request, SearchModuleByIdRequest::class);

        /** @var SearchModuleByIdRequest $request */
        return $this->moduleRepository->find($request->moduleId());
    }
}
