<?php

namespace Core\Profile\Application\UseCasesModule\DeleteModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Exception;

class DeleteModule extends UseCasesService
{
    public function __construct(ModuleRepositoryContract $moduleRepository)
    {
        parent::__construct($moduleRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|Module|Modules
    {
        $this->validateRequest($request, DeleteModuleRequest::class);
        $this->moduleRepository->deleteModule($request->id());
        
        return null;
    }
}