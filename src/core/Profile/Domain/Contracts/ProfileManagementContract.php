<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;

interface ProfileManagementContract
{
    public function searchProfileById(?int $id): ?Profile;

    public function searchProfiles(array $filters = []): Profiles;

    public function updateProfile(int $id, array $data): Profile;

    public function deleteProfile(int $id): void;

    public function createProfile(array $data): Profile;
}
