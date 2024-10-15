<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;

class ProfileTranslator
{
    private ProfileModel $model;
    private array $collection = [];

    public function __construct(
        private readonly ProfileFactoryContract $profileFactory,
    ) {
    }

    public function setModel(ProfileModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function toDomain(): Profile
    {
        $profile = $this->profileFactory->buildProfile(
            $this->profileFactory->buildProfileId($this->model->id()),
            $this->profileFactory->buildProfileName($this->model->name()),
            $this->profileFactory->buildProfileState($this->model->state()),
            $this->profileFactory->buildProfileCreatedAt($this->model->createdAt())
        );

        $profile->setDescription(
            $this->profileFactory->buildProfileDescription($this->model->description())
        );

        $profile->setSearch(
            $this->profileFactory->buildProfileSearch($this->model->search())
        );

        $profile->setUpdatedAt(
            $this->profileFactory->buildProfileUpdateAt($this->model->updatedAt())
        );

        $modulesModel = $this->model->pivotModules();
        $modules = [];
        /** @var Module $item */
        foreach ($modulesModel->get() as $item) {
            if ($item->state() === ValueObjectStatus::STATE_ACTIVE) {
                $modules[] = $item->id();
            }
        }
        $profile->setModulesAggregator($modules);

        return $profile;
    }

    public function setCollection(array $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function toDomainCollection(): Profiles
    {
        $profiles = new Profiles;
        foreach ($this->collection as $id) {
            $profiles->addId($id);
        }

        return $profiles;
    }
}
