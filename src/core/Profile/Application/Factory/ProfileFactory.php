<?php

namespace Core\Profile\Application\Factory;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use DateTime;
use Exception;

class ProfileFactory implements ProfileFactoryContract
{
    /**
     * @throws Exception
     */
    public function buildProfileFromArray(array $data): Profile
    {
        $data = $data[Profile::TYPE];
        $profile = $this->buildProfile(
            $this->buildProfileId($data['id']),
            $this->buildProfileName($data['name']),
            $this->buildProfileState($data['state']),
            $this->buildProfileCreatedAt(
                new DateTime($data['createdAt']['date'])
            )
        );

        $profile->setDescription(
            $this->buildProfileDescription($data['description'])
        );

        $profile->setModulesAggregator($data['modulesAggregator']);

        if ($data['updatedAt']) {
            $profile->updatedAt()->setValue(new DateTime($data['updatedAt']['date']));
        }

        return $profile;
    }

    public function buildProfile(
        ProfileId $id,
        ProfileName $name,
        ProfileState $state = new ProfileState,
        ProfileCreatedAt $createdAt = new ProfileCreatedAt
    ): Profile {

        return new Profile(
            $id,
            $name,
            $state,
            $createdAt
        );
    }

    public function buildProfileId(?int $id = null): ProfileId
    {
        return new ProfileId($id);
    }

    public function buildProfileName(string $name): ProfileName
    {
        return new ProfileName($name);
    }

    /**
     * @throws Exception
     */
    public function buildProfileState(?int $state): ProfileState
    {
        return new ProfileState($state);
    }

    public function buildProfileCreatedAt(?DateTime $datetime): ProfileCreatedAt
    {
        return new ProfileCreatedAt($datetime);
    }

    public function buildProfileUpdateAt(?DateTime $datetime = null): ProfileUpdatedAt
    {
        return new ProfileUpdatedAt($datetime);
    }

    public function buildProfiles(Profile ...$profiles): Profiles
    {
        return new Profiles(...$profiles);
    }

    public function buildProfileSearch(?string $search = null): ProfileSearch
    {
        return new ProfileSearch($search);
    }

    public function buildProfileDescription(?string $description = null): ProfileDescription
    {
        return new ProfileDescription($description);
    }
}
