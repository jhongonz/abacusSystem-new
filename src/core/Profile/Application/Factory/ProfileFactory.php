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
use Core\SharedContext\Model\ValueObjectStatus;

class ProfileFactory implements ProfileFactoryContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function buildProfileFromArray(array $data): Profile
    {
        /** @var array{
         *     id: int,
         *     name: string,
         *     state: int,
         *     description: string|null,
         *     modulesAggregator: array<int<0, max>, int|null>,
         *     updatedAt: string|null,
         *     createdAt: string|null
         * } $dataProfile
         */
        $dataProfile = $data[Profile::TYPE];

        $profile = $this->buildProfile(
            $this->buildProfileId($dataProfile['id']),
            $this->buildProfileName($dataProfile['name']),
        );

        /** @var string $description */
        $description = $dataProfile['description'];
        $profile->description()->setValue($description);

        $profile->setModulesAggregator($dataProfile['modulesAggregator']);

        if (isset($dataProfile['state'])) {
            $profile->state()->setValue($dataProfile['state']);
        }

        if (isset($dataProfile['updatedAt'])) {
            $profile->updatedAt()->setValue($this->getDateTime($dataProfile['updatedAt']));
        }

        if (isset($dataProfile['createdAt'])) {
            $profile->createdAt()->setValue($this->getDateTime($dataProfile['createdAt']));
        }

        return $profile;
    }

    public function buildProfile(
        ProfileId $id,
        ProfileName $name,
        ProfileState $state = new ProfileState(),
        ProfileCreatedAt $createdAt = new ProfileCreatedAt(),
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
     * @throws \Exception
     */
    public function buildProfileState(int $state = ValueObjectStatus::STATE_NEW): ProfileState
    {
        return new ProfileState($state);
    }

    public function buildProfileCreatedAt(\DateTime $datetime): ProfileCreatedAt
    {
        return new ProfileCreatedAt($datetime);
    }

    public function buildProfileUpdateAt(?\DateTime $datetime = null): ProfileUpdatedAt
    {
        return new ProfileUpdatedAt($datetime);
    }

    public function buildProfiles(Profile ...$profiles): Profiles
    {
        return new Profiles($profiles);
    }

    public function buildProfileSearch(?string $search = null): ProfileSearch
    {
        return new ProfileSearch($search);
    }

    public function buildProfileDescription(?string $description = null): ProfileDescription
    {
        return new ProfileDescription($description);
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $dateTime): \DateTime
    {
        return new \DateTime($dateTime);
    }
}
