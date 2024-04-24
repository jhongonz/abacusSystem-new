<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileDeleteException;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class EloquentProfileRepository implements ProfileRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;
    private ProfileModel $model;
    private ProfileTranslator $profileTranslator;
    private DatabaseManager $database;
    private int $priority;

    public function __construct(
        DatabaseManager $database,
        ProfileTranslator $translator,
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->database = $database;
        $this->profileTranslator = $translator;
        $this->priority = $priority;

        $this->model = new ProfileModel();
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function find(ProfileId $id): null|Profile
    {
        try {
            /** @var ProfileModel $profileModel */

            $profileModel = $this->database->table($this->model->getTable())
                ->where('pro_id', $id->value())
                ->where('pro_state','>', ValueObjectStatus::STATE_DELETE)
                ->first();

        } catch (Exception $exception) {
            throw new ProfileNotFoundException('Profile not found with id: '. $id->value());
        }

        return $this->profileTranslator->setModel($profileModel)->toDomain();
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function findCriteria(ProfileName $name): null|Profile
    {
        try {
            /** @var ProfileModel $profileModel */
            $profileModel = $this->database->table($this->model->getTable())
                ->where('pro_name', $name->value())
                ->where('pro_state','>', ValueObjectStatus::STATE_DELETE)
                ->first();

        } catch (Exception $exception) {
            throw new ProfileNotFoundException('Profile not found with name: '. $name->value());
        }

        return $this->profileTranslator->setModel($profileModel)->toDomain();
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws Exception
     */
    public function getAll(array $filters = []): Profiles
    {
        try {
            /**@var Builder $queryBuilder*/
            $queryBuilder = $this->database->table($this->model->getTable())
                ->where('pro_state','>',ValueObjectStatus::STATE_DELETE);

            if (array_key_exists('q', $filters) && isset($filters['q'])) {
                $queryBuilder->where('pro_name','like','%'.$filters['q'].'%');
            }

            /**@var Collection $profileModel*/
            $profileModel = $queryBuilder->get(['pro_id']);
        } catch (Exception $exception) {
            throw new ProfilesNotFoundException('Profiles not found');
        }

        $collection = [];
        /**@var ProfileModel $item*/
        foreach($profileModel as $item) {
            $collection[] = $item->id();
        }

        $profiles = $this->profileTranslator->setCollection($collection)->toDomainCollection();
        $profiles->setFilters($filters);

        return $profiles;
    }

    /**
     * @throws ProfileDeleteException
     * @throws ProfileNotFoundException
     */
    public function deleteProfile(ProfileId $id): void
    {
        /**@var ProfileModel $profileModel*/
        $profileModel = $this->database->table($this->model->getTable())->find($id->value());

        if (is_null($profileModel)) {
            throw new ProfileNotFoundException('Profile not found with id: '. $id->value());
        }

        try {
            $profileModel->pivotModules()->detach();
            $profileModel->deleteOrFail();
        } catch (Throwable $e) {
            throw new ProfileDeleteException('Profile can not be deleted with id: '.$id->value(), $e->getTrace());
        }
    }

    public function persistProfile(Profile $profile): Profile
    {
        /** @var ProfileModel $profileModel */
        $profileModel = $this->domainToModel($profile);
        $profileModel->save();
        $profile->id()->setValue($profileModel->id());

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

    protected function domainToModel(Profile $domain, ?ProfileModel $model = null): Profile
    {
        if (is_null($model)) {
            $model = $this->database->table($this->model->getTable())->find($domain->id()->value()) ?: $this->createModel();
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

    protected function createModel(): ProfileModel
    {
        return $this->model;
    }
}
