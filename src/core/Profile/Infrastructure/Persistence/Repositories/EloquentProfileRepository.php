<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use App\Models\Profile as ProfileModel;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Exceptions\ProfileDeleteException;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Core\Profile\Infrastructure\Persistence\Translators\TranslatorContract;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class EloquentProfileRepository implements ProfileRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;
    private ProfileModel $profileModel;
    private ProfileTranslator $profileTranslator;
    private TranslatorContract $modelProfileTranslator;
    private int $priority;

    public function __construct(
      ProfileModel $model,
      ProfileTranslator $translator,
      TranslatorContract $modelProfileTranslator,
      int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->profileModel = $model;
        $this->profileTranslator = $translator;
        $this->modelProfileTranslator = $modelProfileTranslator;
        $this->priority = $priority;
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function find(ProfileId $id): null|Profile
    {
        try {
            /** @var ProfileModel $profileModel */
            $profileModel = $this->profileModel
                ->where('pro_id', $id->value())
                ->where('pro_state','>',ProfileState::STATE_DELETE)
                ->firstOrFail();
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
            $profileModel = $this->profileModel
                ->where('pro_name', $name->value())
                ->where('pro_state','>',ProfileState::STATE_DELETE)
                ->firstOrFail();
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
            $queryBuilder = $this->profileModel
                ->where('pro_state','>',ProfileState::STATE_DELETE);

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

    public function save(Profile $profile): void
    {
        // TODO: Implement save() method.
    }

    public function update(ProfileId $id, Profile $profile): void
    {
        // TODO: Implement update() method.
    }

    /**
     * @throws ProfileDeleteException
     * @throws ProfileNotFoundException
     */
    public function deleteProfile(ProfileId $id): void
    {
        /**@var ProfileModel $profileModel*/
        $profileModel = $this->profileModel->where('pro_id', $id->value())
            ->where('pro_state','>', ProfileState::STATE_DELETE)
            ->first();

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
        $profileModel = $this->modelProfileTranslator->executeTranslate($profile);
        $profileModel->save();


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
}
