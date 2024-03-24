<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilePersistException;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Exception;
use Illuminate\Support\Facades\Redis;

class RedisProfileRepository implements ProfileRepositoryContract, ChainPriority
{
    /**@var int*/
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const PROFILE_KEY_FORMAT = '%s::%s';

    private int $priority;
    private string $keyPrefix;

    private ProfileFactoryContract $profileFactory;
    private ProfileDataTransformerContract $dataTransformer;

    public function __construct(
      ProfileFactoryContract $profileFactory,
      ProfileDataTransformerContract $dataTransformer,
      string $keyPrefix = 'profile',
      int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->profileFactory = $profileFactory;
        $this->dataTransformer = $dataTransformer;
        $this->keyPrefix = $keyPrefix;
        $this->priority = $priority;
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

    /**
     * @throws ProfileNotFoundException
     */
    public function find(ProfileId $id): null|Profile
    {
        try {
            $data = Redis::get($this->profileKey($id));
        } catch (Exception $exception) {
            throw new ProfileNotFoundException('Profile not found by id '. $id->value());
        }

        if (!is_null($data)) {
            $dataArray = json_decode($data, true);
            return $this->profileFactory->buildProfileFromArray($dataArray);
        }

        return null;
    }

    public function findCriteria(ProfileName $name): null|Profile
    {
        // TODO: Implement findCriteria() method.
    }

    public function getAll(array $filters = []): null|Profiles
    {
        return null;
    }

    public function save(Profile $profile): void
    {
        // TODO: Implement save() method.
    }

    public function update(ProfileId $id, Profile $profile): void
    {
        // TODO: Implement update() method.
    }

    public function deleteProfile(ProfileId $id): void
    {
        Redis::delete($this->profileKey($id));
    }

    /**
     * @throws ProfilePersistException
     */
    public function persistProfile(Profile $profile): Profile
    {
        $profileKey = $this->profileKey($profile->id());

        try {
            $profileData = $this->dataTransformer->write($profile)->read();
            Redis::set($profileKey, json_encode($profileData));
        } catch (Exception $exception) {
            throw new ProfilePersistException('It could not persist Profile with key '.$profileKey.' in redis');
        }

        return $profile;
    }

    public function persistProfiles(Profiles $profiles): Profiles
    {
        return $profiles;
    }

    private function profileKey(ProfileId $id): string
    {
        return sprintf(self::PROFILE_KEY_FORMAT, $this->keyPrefix, $id->value());
    }
}
