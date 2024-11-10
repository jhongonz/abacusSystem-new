<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;

interface ProfileRepositoryContract
{
    public function find(ProfileId $id): ?Profile;

    public function findCriteria(ProfileName $name): ?Profile;

    /**
     * @param array<string, mixed> $filters
     * @return Profiles|null
     */
    public function getAll(array $filters = []): ?Profiles;

    public function deleteProfile(ProfileId $id): void;

    public function persistProfile(Profile $profile): Profile;
}
