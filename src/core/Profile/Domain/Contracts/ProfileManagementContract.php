<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;

interface ProfileManagementContract
{
    public function searchProfileById(ProfileId $id): null|Profile;

    public function searchProfiles(array $filters = []): Profiles;

    public function updateProfile(ProfileId $id, array $data): void;

    public function deleteProfile(ProfileId $id): void;

    public function createProfile(Profile $profile): void;
}
