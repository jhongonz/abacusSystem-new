<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use App\Models\Module as ModuleModel;
use Core\Profile\Domain\Module;

class DomainToModelModuleTranslator implements TranslatorContract
{
    private ModuleModel $model;
    private string $canTranslator;
    
    public function __construct(
        ModuleModel $model,
    ) {
        $this->model = $model;
        $this->canTranslator = Module::class;
    }

    /**
     * @param Module $source
     * @param null|ModuleModel $destiny
     * @return ModuleModel
     */
    public function executeTranslate($source, $destiny = null): ModuleModel
    {
        if (is_null($destiny)) {
            $destiny = $this->model->where('mod_id', $source->id()->value())->first() ?: $this->createModel();
        }
        
        $destiny->changeId($source->id()->value());
        $destiny->changeName($source->name()->value());
        $destiny->changeMenuKey($source->menuKey()->value());
        $destiny->changeRoute($source->route()->value());
        $destiny->changeIcon($source->icon()->value());
        $destiny->changeState($source->state()->value());
        $destiny->changeSearch($source->search()->value());
        $destiny->changeCreatedAt($source->createdAt()->value());
        
        if (!is_null($source->updatedAt()->value())) {
            $destiny->changeUpdatedAt($source->updatedAt()->value());
        }
        
        return $destiny;
    }

    public function canTranslate(): string
    {
        return $this->canTranslator;
    }

    public function canTranslateTo(): string
    {
        return ModuleModel::class;
    }
    
    private function createModel(): ModuleModel
    {
        return new ModuleModel();
    }
}