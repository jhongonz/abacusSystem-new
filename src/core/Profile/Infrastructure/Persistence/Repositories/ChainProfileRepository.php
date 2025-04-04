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

class ChainProfileRepository extends AbstractChainRepository implements ProfileRepositoryContract
{
    private const FUNCTION_NAME = 'persistProfile';

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAME;
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \Throwable
     */
    public function find(ProfileId $id): ?Profile
    {
        try {
            /** @var Profile|null $result */
            $result = $this->read(__FUNCTION__, $id);

            return $result;
        } catch (\Exception $exception) {
            throw new ProfileNotFoundException('Profile not found by id '.$id->value());
        }
    }

    /**
     * @throws \Throwable
     */
    public function findCriteria(ProfileName $name): ?Profile
    {
        try {
            /** @var Profile|null $result */
            $result = $this->read(__FUNCTION__, $name);

            return $result;
        } catch (\Exception $exception) {
            throw new ProfileNotFoundException('Profile not found by name '.$name->value());
        }
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws \Throwable
     */
    public function getAll(array $filters = []): ?Profiles
    {
        $this->canPersist = false;

        try {
            /** @var Profiles|null $result */
            $result = $this->read(__FUNCTION__, $filters);

            return $result;
        } catch (\Exception $exception) {
            throw new ProfilesNotFoundException('Profiles not found');
        }
    }

    /**
     * @throws \Throwable
     */
    public function deleteProfile(ProfileId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    /**
     * @throws \Exception
     */
    public function persistProfile(Profile $profile): Profile
    {
        /** @var Profile $result */
        $result = $this->write(__FUNCTION__, $profile);

        return $result;
    }
}
