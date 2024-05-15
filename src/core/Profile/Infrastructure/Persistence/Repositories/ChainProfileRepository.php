<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Exception;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class ChainProfileRepository extends AbstractChainRepository implements ProfileRepositoryContract
{
    private const FUNCTION_NAMES = [
        Profile::class => 'persistProfile',
        Profiles::class => 'persistProfiles',
    ];

    private string $domainToPersist;

    private bool $deleteSource = false;

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Throwable
     */
    public function find(ProfileId $id): ?Profile
    {
        $this->domainToPersist = Profile::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ProfileNotFoundException($exception->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function findCriteria(ProfileName $name): ?Profile
    {
        $this->domainToPersist = Profile::class;

        try {
            return $this->read(__FUNCTION__, $name);
        } catch (Exception $exception) {
            throw new ProfileNotFoundException($exception->getMessage());
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
            throw new ProfilesNotFoundException($exception->getMessage());
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
        try {
            $this->write(__FUNCTION__, $id);
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

    public function functionNameDelete(): bool
    {
        return $this->deleteSource;
    }
}
