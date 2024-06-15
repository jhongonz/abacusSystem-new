<?php

namespace Core\Profile\Infrastructure\Management;

use Core\Profile\Application\UseCasesModule\CreateModule\CreateModule;
use Core\Profile\Application\UseCasesModule\CreateModule\CreateModuleRequest;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModule;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleById;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleByIdRequest;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModules;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModulesRequest;
use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModule;
use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModuleRequest;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Exception;

class ModuleService implements ModuleManagementContract
{
    private ModuleFactoryContract $moduleFactory;
    private SearchModuleById $searchModuleById;
    private SearchModules $searchModules;
    private UpdateModule $updateModule;
    private DeleteModule $deleteModule;
    private CreateModule $createModule;

    public function __construct(
        ModuleFactoryContract $moduleFactory,
        SearchModuleById $searchModuleById,
        SearchModules $searchModules,
        UpdateModule $updateModule,
        DeleteModule $deleteModule,
        CreateModule $createModule,
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->searchModuleById = $searchModuleById;
        $this->searchModules = $searchModules;
        $this->updateModule = $updateModule;
        $this->deleteModule = $deleteModule;
        $this->createModule = $createModule;
    }

    /**
     * @throws Exception
     */
    public function searchModuleById(?int $id): ?Module
    {
        $request = new SearchModuleByIdRequest(
            $this->moduleFactory->buildModuleId($id)
        );

        return $this->searchModuleById->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchModules(array $filters = []): Modules
    {
        $request = new SearchModulesRequest($filters);

        $modules = $this->searchModules->execute($request);
        foreach ($modules->aggregator() as $item) {
            $module = $this->searchModuleById($item);
            $modules->addItem($module);
        }

        return $modules;
    }

    /**
     * @throws Exception
     */
    public function updateModule(int $moduleId, array $data): Module
    {
        $request = new UpdateModuleRequest(
            $this->moduleFactory->buildModuleId($moduleId),
            $data
        );

        return $this->updateModule->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteModule(int $moduleId): void
    {
        $request = new DeleteModuleRequest(
            $this->moduleFactory->buildModuleId($moduleId)
        );

        $this->deleteModule->execute($request);
    }

    /**
     * @throws Exception
     */
    public function createModule(array $dataModule): Module
    {
        $module = $this->moduleFactory->buildModuleFromArray($dataModule);
        $request = new CreateModuleRequest($module);

        return $this->createModule->execute($request);
    }
}
