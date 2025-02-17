<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\SharedContext\Model\ValueObjectStatus;

class ProfileTranslator
{
    private ProfileModel $model;

    /**
     * @var array<int<0, max>, int>
     */
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
     * @throws \Exception
     */
    public function toDomain(): Profile
    {
        $profile = $this->profileFactory->buildProfile(
            $this->profileFactory->buildProfileId($this->model->id()),
            $this->profileFactory->buildProfileName($this->model->name() ?? ''),
            $this->profileFactory->buildProfileState($this->model->state())
        );

        $profile->setDescription(
            $this->profileFactory->buildProfileDescription($this->model->description())
        );

        $profile->setSearch(
            $this->profileFactory->buildProfileSearch($this->model->search())
        );

        if (!is_null($this->model->createdAt())) {
            $profile->setCreatedAt(
                $this->profileFactory->buildProfileCreatedAt($this->model->createdAt())
            );
        }

        if (!is_null($this->model->updatedAt())) {
            $profile->setUpdatedAt(
                $this->profileFactory->buildProfileUpdateAt($this->model->updatedAt())
            );
        }

        $modulesModel = $this->model->pivotModules();
        $modules = [];

        /** @var Module $item */
        foreach ($modulesModel->get() as $item) {
            if (ValueObjectStatus::STATE_ACTIVE === $item->state()) {
                $modules[] = $item->id();
            }
        }

        $profile->setModulesAggregator($modules);
        return $profile;
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

    public function toDomainCollection(): Profiles
    {
        $profiles = new Profiles();
        foreach ($this->collection as $id) {
            $profiles->addId($id);
        }

        return $profiles;
    }
}
