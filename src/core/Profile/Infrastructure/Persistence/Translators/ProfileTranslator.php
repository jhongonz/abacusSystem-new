<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

use App\Models\Module;
use App\Models\Profile as ProfileModel;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;
use Core\SharedContext\Model\ValueObjectStatus;
use DateTime;
use Exception;

class ProfileTranslator implements TranslatorDomainContract
{
    private ProfileFactoryContract $profileFactory;
    private ProfileModel $model;
    private array $collection;

    public function __construct(
        ProfileFactoryContract $profileFactory,
        ProfileModel $model,
        array $collection = [],
    ) {
        $this->profileFactory = $profileFactory;
        $this->model = $model;
        $this->collection = $collection;
    }

    /**
     * @param ProfileModel $model
     * @return $this
     */
    public function setModel($model): self
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
            $this->profileFactory->buildProfileCreatedAt(
                new DateTime($this->model->createdAt())    
            )
        );
        
        $profile->setUpdatedAt(
            $this->profileFactory->buildProfileUpdateAt(
                new DateTime($this->model->updatedAt())
            )
        );
        
        $modulesModel = $this->model->modules();
        $modules = array();
        /** @var Module $item */
        foreach($modulesModel as $item) {
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
        $profiles = new Profiles();
        foreach ($this->collection as $id) {
            $profiles->addId($id);
        }
        
        return $profiles;
    }

    public function canTranslate(): string
    {
        return ProfileModel::class;
    }
}