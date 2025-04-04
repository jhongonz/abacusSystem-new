<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module as ModuleModel;

class ModuleTranslator
{
    private ModuleModel $model;

    /**
     * @var array<int<0, max>, int>
     */
    private array $collection = [];

    public function __construct(
        private readonly ModuleFactoryContract $moduleFactory,
    ) {
    }

    public function setModel(ModuleModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function toDomain(): Module
    {
        $module = $this->moduleFactory->buildModule(
            $this->moduleFactory->buildModuleId($this->model->id()),
            $this->moduleFactory->buildModuleMenuKey($this->model->menuKey() ?? ''),
            $this->moduleFactory->buildModuleName($this->model->name()),
            $this->moduleFactory->buildModuleRoute($this->model->route() ?? ''),
            $this->moduleFactory->buildModuleIcon($this->model->icon()),
            $this->moduleFactory->buildModuleState($this->model->state()),
            $this->moduleFactory->buildModuleCreatedAt($this->model->createdAt())
        );

        $module->setPosition($this->moduleFactory->buildModulePosition($this->model->position()));

        $module->setSearch(
            $this->moduleFactory->buildModuleSearch($this->model->search())
        );

        $module->setUpdatedAt($this->moduleFactory->buildModuleUpdatedAt($this->model->updatedAt()));

        return $module;
    }

    /**
     * @param array<int<0, max>, int> $collection
     *
     * @return $this
     */
    public function setCollection(array $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function toDomainCollection(): Modules
    {
        $modules = new Modules();
        foreach ($this->collection as $id) {
            $modules->addId($id);
        }

        return $modules;
    }
}
