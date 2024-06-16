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
use Psr\Log\LoggerInterface;

class RedisProfileRepository implements ChainPriority, ProfileRepositoryContract
{
    /** @var int */
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const PROFILE_KEY_FORMAT = '%s::%s';

    private int $priority;

    private string $keyPrefix;

    private ProfileFactoryContract $profileFactory;

    private ProfileDataTransformerContract $dataTransformer;
    private LoggerInterface $logger;

    public function __construct(
        ProfileFactoryContract $profileFactory,
        ProfileDataTransformerContract $dataTransformer,
        LoggerInterface $logger,
        string $keyPrefix = 'profile',
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->profileFactory = $profileFactory;
        $this->dataTransformer = $dataTransformer;
        $this->logger = $logger;
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
     * @throws Exception
     */
    public function find(ProfileId $id): ?Profile
    {
        try {
            $data = Redis::get($this->profileKey($id));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new ProfileNotFoundException('Profile not found by id '.$id->value());
        }

        if (! is_null($data)) {
            $dataArray = json_decode($data, true);

            /** @var Profile */
            return $this->profileFactory->buildProfileFromArray($dataArray);
        }

        return null;
    }

    /**
     * @throws ProfileNotFoundException
     */
    public function findCriteria(ProfileName $name): ?Profile
    {
        try {
            $data = Redis::get($this->profileKeyWithName($name));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new ProfileNotFoundException('Profile not found by name '.$name->value());
        }

        if (! is_null($data)) {
            $dataArray = json_decode($data, true);

            return $this->profileFactory->buildProfileFromArray($dataArray);
        }

        return null;
    }

    public function getAll(array $filters = []): ?Profiles
    {
        return null;
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
        $profileKeyName = $this->profileKeyWithName($profile->name());

        try {
            $profileData = $this->dataTransformer->write($profile)->read();
            Redis::set($profileKey, json_encode($profileData));
            Redis::set($profileKeyName, json_encode($profileData));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
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

    private function profileKeyWithName(ProfileName $name): string
    {
        return sprintf(self::PROFILE_KEY_FORMAT, $this->keyPrefix, $name->value());
    }
}
