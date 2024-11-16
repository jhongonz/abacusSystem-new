<?php

namespace Core\Profile\Application\UseCasesModule\DeleteModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;

class DeleteModule extends UseCasesService
{
    public function __construct(ModuleRepositoryContract $moduleRepository)
    {
        parent::__construct($moduleRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): null
    {
        $this->validateRequest($request, DeleteModuleRequest::class);

        /* @var DeleteModuleRequest $request */
        $this->moduleRepository->deleteModule($request->id());

        return null;
    }
}
