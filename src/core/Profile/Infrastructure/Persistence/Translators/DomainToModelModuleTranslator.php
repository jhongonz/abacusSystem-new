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
     * @param Module $domain
     * @param null|ModuleModel $model
     * @return ModuleModel
     */
    public function executeTranslate($domain, $model = null): ModuleModel
    {
        if (is_null($model)) {
            $model = $this->model->where('mod_id', $domain->id()->value())->first() ?: $this->createModel();
        }

        $model->changeId($domain->id()->value());
        $model->changeName($domain->name()->value());
        $model->changeMenuKey($domain->menuKey()->value());
        $model->changeRoute($domain->route()->value());
        $model->changeIcon($domain->icon()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
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
