<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use App\Models\Module as ModuleModel;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;
use DateTime;
use Exception;

class ModuleTranslator implements TranslatorDomainContract
{
    private ModuleModel $model;
    private ModuleFactoryContract $moduleFactory;
    private array $collection;
    
    public function __construct(
        ModuleModel $model,
        ModuleFactoryContract $factoryContract,
        array $collection = [],
    ) {
        $this->model = $model;
        $this->moduleFactory = $factoryContract;
        $this->collection = $collection;
    }

    /**
     * @param ModuleModel $model
     * @return self
     */
    public function setModel($model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function toDomain(): Module
    {
        $module = $this->moduleFactory->buildModule(
            $this->moduleFactory->buildModuleId($this->model->id()),
            $this->moduleFactory->buildModuleMenuKey($this->model->menuKey()),
            $this->moduleFactory->buildModuleName($this->model->name()),
            $this->moduleFactory->buildModuleRoute($this->model->route()),
            $this->moduleFactory->buildModuleIcon($this->model->icon()),
            $this->moduleFactory->buildModuleState($this->model->state()),
            $this->moduleFactory->buildModuleCreatedAt(
                new DateTime($this->model->createdAt())    
            ));
        
        $module->setUpdatedAt($this->moduleFactory->buildModuleUpdatedAt(
            new DateTime($this->model->updatedAt())
        ));
        
        return $module;
    }

    public function setCollection(array $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    public function toDomainCollection(): Modules
    {
        $modules = new Modules();
        foreach($this->collection as $id) {
            $modules->addId($id);
        }
        
        return $modules;
    }

    public function canTranslate(): string
    {
        return ModuleModel::class;
    }
}