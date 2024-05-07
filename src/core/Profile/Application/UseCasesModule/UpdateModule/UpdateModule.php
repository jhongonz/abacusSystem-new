<?php

namespace Core\Profile\Application\UseCasesModule\UpdateModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Exception;

class UpdateModule extends UseCasesService
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
        $this->validateRequest($request, UpdateModuleRequest::class);

        $module = $this->moduleRepository->find($request->moduleId());
        foreach ($request->data() as $field => $value) {
            $methodName = 'change'.\ucfirst($field);

            if (\is_callable([$this, $methodName])) {
                $module = $this->{$methodName}($module, $value);
            }
        }

        $module->refreshSearch();

        return $this->moduleRepository->persistModule($module);
    }

    private function changeName(Module $module, string $value): Module
    {
        $module->name()->setValue($value);

        return $module;
    }

    private function changeRoute(Module $module, string $value): Module
    {
        $module->route()->setValue($value);

        return $module;
    }

    private function changeIcon(Module $module, string $value): Module
    {
        $module->icon()->setValue($value);

        return $module;
    }

    private function changeKey(Module $module, string $value): Module
    {
        $module->menuKey()->setValue($value);

        return $module;
    }

    /**
     * @throws Exception
     */
    private function changeState(Module $module, int $value): Module
    {
        $module->state()->setValue($value);

        return $module;
    }
}
