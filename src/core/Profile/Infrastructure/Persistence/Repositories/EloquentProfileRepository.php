<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

class EloquentProfileRepository implements ChainPriority, ProfileRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    private ProfileModel $model;

    private ProfileTranslator $profileTranslator;

    private DatabaseManager $database;

    private int $priority;

    public function __construct(
        DatabaseManager $database,
        ProfileTranslator $translator,
        ProfileModel $model,
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->database = $database;
        $this->profileTranslator = $translator;
        $this->model = $model;
        $this->priority = $priority;
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function find(ProfileId $id): Profile
    {
        $builder = $this->database->table($this->getTable());

        $data = $builder->where('pro_id', $id->value())
            ->where('pro_state', '>', ValueObjectStatus::STATE_DELETE)
            ->first();

        if (is_null($data)) {
            throw new ProfileNotFoundException('Profile not found with id: '.$id->value());
        }

        $profileModel = $this->updateAttributesModelProfile($data->toArray());

        return $this->profileTranslator->setModel($profileModel)->toDomain();
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function findCriteria(ProfileName $name): ?Profile
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('pro_name', $name->value())
            ->where('pro_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();

        if (is_null($data)) {
            throw new ProfileNotFoundException('Profile not found with name: '.$name->value());
        }

        $profileModel = $this->updateAttributesModelProfile($data->toArray());

        return $this->profileTranslator->setModel($profileModel)->toDomain();
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws Exception
     */
    public function getAll(array $filters = []): Profiles
    {
        /** @var Builder $builder */
        $builder = $this->database->table($this->getTable());
        $builder->where('pro_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }

        $profileCollection = $builder->get(['pro_id']);

        if (is_null($profileCollection)) {
            throw new ProfilesNotFoundException('Profiles not found');
        }

        $collection = [];
        /** @var ProfileModel $profileModel*/
        foreach ($profileCollection as $profileModel) {
            $collection[] = $profileModel->id();
        }

        $profiles = $this->profileTranslator->setCollection($collection)->toDomainCollection();
        $profiles->setFilters($filters);

        return $profiles;
    }

    /**
     * @throws ProfileNotFoundException
     */
    public function deleteProfile(ProfileId $id): void
    {
        $builder = $this->database->table($this->getTable());

        /** @var ProfileModel|null $profileModel */
        $profileModel = $builder->find($id->value());

        if (is_null($profileModel)) {
            throw new ProfileNotFoundException('Profile not found with id: '.$id->value());
        }

        $profileModel->pivotModules()->detach();
        $builder->where('pro_id', $id->value());
        $builder->delete();
    }

    public function persistProfile(Profile $profile): Profile
    {
        $profileModel = $this->domainToModel($profile);
        $profileId = $profileModel->id();
        $dataModel = $profileModel->toArray();

        $builder = $this->database->table($this->getTable());

        if (is_null($profileId)) {
            $profileId = $builder->insertGetId($dataModel);
            $profile->id()->setValue($profileId);
        } else {
            $builder->where('pro_id', $profileId);
            $builder->update($dataModel);
        }

        $profileModel->pivotModules()->sync($profile->modulesAggregator());

        return $profile;
    }

    public function persistProfiles(Profiles $profiles): Profiles
    {
        return $profiles;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function changePriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    private function domainToModel(Profile $domain): ProfileModel
    {
        $builder = $this->database->table($this->getTable());
        $data = $builder->find($domain->id()->value());
        $model = $this->updateAttributesModelProfile((array) $data);

        $model->changeId($domain->id()->value());
        $model->changeName($domain->name()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeDescription($domain->description()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (! is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    private function updateAttributesModelProfile(array $data = []): ProfileModel
    {
        $this->model->fill($data);
        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }
}
