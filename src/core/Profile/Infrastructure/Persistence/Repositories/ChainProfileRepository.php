<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Exception;
use Throwable;

class ChainProfileRepository extends AbstractChainRepository implements ProfileRepositoryContract
{
    private const FUNCTION_NAMES = [
        Profile::class => 'persistProfile',
        Profiles::class => 'persistProfiles',
    ];

    private string $domainToPersist;
    private bool $deleteSource = false;

    function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Throwable
     */
    public function find(ProfileId $id): null|Profile
    {
        $this->domainToPersist = Profile::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ProfileNotFoundException('Profile not found by id '. $id->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function findCriteria(ProfileName $name): null|Profile
    {
        $this->domainToPersist = Profile::class;

        try {
            return $this->read(__FUNCTION__, $name);
        } catch (Exception $exception) {
            throw new ProfileNotFoundException('Profile not found by name '. $name->value());
        }
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws Throwable
     */
    public function getAll(array $filters = []): Profiles
    {
        $this->domainToPersist = Profiles::class;

        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new ProfilesNotFoundException('Profiles no found');
        }
    }

    public function save(Profile $profile): void
    {
        $this->persistence(__FUNCTION__, $profile);
    }

    public function update(ProfileId $id, Profile $profile): void
    {
        $this->persistence(__FUNCTION__, $id, $profile);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Throwable
     */
    public function deleteProfile(ProfileId $id): void
    {
        $this->deleteSource = true;

        try {
            $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ProfileNotFoundException($exception->getMessage());
        }
    }

    public function persistProfile(Profile $profile): Profile
    {
        return $this->write(__FUNCTION__, $profile);
    }

    public function persistProfiles(Profiles $profiles): Profiles
    {
        return $this->write(__FUNCTION__, $profiles);
    }

    function functionNameDelete(): bool
    {
        return $this->deleteSource;
    }
}
