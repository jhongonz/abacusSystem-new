<?php

namespace Core\Profile\Application\UseCasesModule\CreateModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Exception;

class CreateModule extends UseCasesService
{
    public function __construct(ModuleRepositoryContract $moduleRepository)
    {
        parent::__construct($moduleRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): Module
    {
        $this->validateRequest($request, CreateModuleRequest::class);
        
        /**@var Module $module*/
        $module = $request->module();
        $module->refreshSearch();
        
        return $this->moduleRepository->persistModule($module);
    }
}