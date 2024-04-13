<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use App\Models\Profile as ProfileModel;
use Core\Profile\Domain\Profile;

class DomainToModelProfileTranslator implements TranslatorContract
{
    private ProfileModel $model;
    private string $canTranslate;

    public function __construct(ProfileModel $model)
    {
        $this->model = $model;
        $this->canTranslate = Profile::class;
    }

    /**
     * @param Profile $domain
     * @param ProfileModel $model
     * @return ProfileModel
     */
    public function executeTranslate($domain, $model = null): ProfileModel
    {
        if (is_null($model)) {
            $model = $this->model->where('pro_id', $domain->id()->value())->first() ?: $this->createModel();
        }

        $model->changeId($domain->id()->value());
        $model->changeName($domain->name()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeDescription($domain->description()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    public function canTranslate(): string
    {
        return $this->canTranslate;
    }

    public function canTranslateTo(): string
    {
        return ProfileModel::class;
    }

    protected function createModel(): ProfileModel
    {
        return new ProfileModel();
    }
}
