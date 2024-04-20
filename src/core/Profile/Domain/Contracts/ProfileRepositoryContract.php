<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;

interface ProfileRepositoryContract
{
    public function find(ProfileId $id): null|Profile;

    public function findCriteria(ProfileName $name): null|Profile;

    public function getAll(array $filters = []): null|Profiles;

    public function deleteProfile(ProfileId $id): void;

    public function persistProfile(Profile $profile): Profile;
}
