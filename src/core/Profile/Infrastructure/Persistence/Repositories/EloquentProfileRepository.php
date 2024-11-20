<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Database\DatabaseManager;

class EloquentProfileRepository implements ChainPriority, ProfileRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    public function __construct(
        private readonly DatabaseManager $database,
        private readonly ProfileTranslator $profileTranslator,
        private readonly ProfileModel $model,
        private int $priority = self::PRIORITY_DEFAULT,
    ) {
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \Exception
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

        $profileModel = $this->updateAttributesModelProfile((array) $data);

        return $this->profileTranslator->setModel($profileModel)->toDomain();
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \Exception
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

        $profileModel = $this->updateAttributesModelProfile((array) $data);

        return $this->profileTranslator->setModel($profileModel)->toDomain();
    }

    /**
     * @param array{q?: string|null} $filters
     */
    public function getAll(array $filters = []): Profiles
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('pro_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }

        $profileCollection = $builder->get(['pro_id']);

        $collection = [];
        foreach ($profileCollection as $item) {
            $profileModel = $this->updateAttributesModelProfile((array) $item);

            if (!is_null($profileModel->id())) {
                $collection[] = $profileModel->id();
            }
        }

        $profiles = $this->profileTranslator->setCollection($collection)->toDomainCollection();
        $profiles->setFilters($filters);

        return $profiles;
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \Exception
     */
    public function deleteProfile(ProfileId $id): void
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('pro_id', $id->value());
        $data = $builder->first();

        if (is_null($data)) {
            throw new ProfileNotFoundException('Profile not found with id: '.$id->value());
        }

        $profileModel = $this->updateAttributesModelProfile((array) $data);
        $profileModel->changeState(ValueObjectStatus::STATE_DELETE);
        $profileModel->changeDeletedAt($this->getDateTime());

        $dataModel = $profileModel->toArray();
        $builder->update($dataModel);

        $profileDomain = $this->profileTranslator->setModel($profileModel)->toDomain();
        $profileDomain->setModulesAggregator([]);
        $this->syncPrivileges($profileDomain, $profileModel);
    }

    /**
     * @throws \Exception
     */
    public function persistProfile(Profile $profile): Profile
    {
        $profileModel = $this->domainToModel($profile);
        $profileId = $profileModel->id();
        $dataModel = $profileModel->toArray();

        $builder = $this->database->table($this->getTable());

        if (is_null($profileId)) {
            $dataModel['created_at'] = $this->getDateTime();

            $profileId = $builder->insertGetId($dataModel);
            $profile->id()->setValue($profileId);
            $profile->createdAt()->setValue($dataModel['created_at']);
        } else {
            $dataModel['updated_at'] = $this->getDateTime();
            $profile->updatedAt()->setValue($dataModel['updated_at']);

            $builder->where('pro_id', $profileId);
            $builder->update($dataModel);
        }

        $this->syncPrivileges($profile, $profileModel);

        return $profile;
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
        $builder->where('pro_id', $domain->id()->value());
        $data = $builder->first();
        $model = $this->updateAttributesModelProfile((array) $data);

        $model->changeId($domain->id()->value());
        $model->changeName($domain->name()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeDescription($domain->description()->value() ?? '');
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updateAttributesModelProfile(array $data = []): ProfileModel
    {
        $this->model->fill($data);
        $this->model->exists = true;

        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }

    private function syncPrivileges(Profile $profile, ProfileModel $model): void
    {
        $profileId = $profile->id()->value();
        $pivotTable = $model->pivotModules()->getTable();

        $builder = $this->database->table($pivotTable);
        $builder->where('pri__pro_id', $profileId);
        $builder->delete();

        foreach ($profile->modulesAggregator() as $item) {
            $builder->insert([
                'pri__pro_id' => $profileId,
                'pri__mod_id' => $item,
            ]);
        }
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $datetime = 'now'): \DateTime
    {
        return new \DateTime($datetime);
    }
}
