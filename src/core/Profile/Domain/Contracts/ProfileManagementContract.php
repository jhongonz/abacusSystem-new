<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;

interface ProfileManagementContract
{
    public function searchProfileById(?int $id): ?Profile;

    /**
     * @param array<string, mixed> $filters
     * @return Profiles
     */
    public function searchProfiles(array $filters = []): Profiles;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Profile
     */
    public function updateProfile(int $id, array $data): Profile;

    public function deleteProfile(int $id): void;

    /**
     * @param array<string, mixed> $data
     * @return Profile
     */
    public function createProfile(array $data): Profile;
}
