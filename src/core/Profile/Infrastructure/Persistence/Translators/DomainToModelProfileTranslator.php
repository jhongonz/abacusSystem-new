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
     * @param Profile $source
     * @param ProfileModel $destiny
     * @return ProfileModel
     */
    public function executeTranslate($source,$destiny = null): ProfileModel
    {
        if (is_null($destiny)) {
            $destiny = $this->model->where('pro_id', $source->id()->value())->first() ?: $this->createModel();
        }

        $destiny->changeId($source->id()->value());
        $destiny->changeName($source->name()->value());
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
