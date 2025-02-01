<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;

interface ProfileManagementContract
{
    public function searchProfileById(?int $id): ?Profile;

    /**
     * @param array<string, mixed> $filters
     */
    public function searchProfiles(array $filters = []): Profiles;

    /**
     * @param array<string, mixed> $data
     */
    public function updateProfile(int $id, array $data): Profile;

    public function deleteProfile(int $id): void;

    /**
     * @param array<string, mixed> $data
     */
    public function createProfile(array $data): Profile;
}
