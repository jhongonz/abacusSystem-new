<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use DateTime;

interface ProfileFactoryContract
{
    public function buildProfileFromArray(array $data): Profile;
    
    public function buildProfile(
        ProfileId $id,
        ProfileName $name,
        ProfileState $state = new ProfileState(),
        ProfileCreatedAt $createdAt = new ProfileCreatedAt(),
    ): Profile;
    
    public function buildProfileId(null|int $id = null): ProfileId;
    
    public function buildProfileName(string $name): ProfileName;
    
    public function buildProfileState(null|int $state): ProfileState;
    
    public function buildProfileSearch(null|string $search = null): ProfileSearch;
    
    public function buildProfileCreatedAt(null|DateTime $datetime): ProfileCreatedAt;
    
    public function buildProfileUpdateAt(null|DateTime $datetime): ProfileUpdatedAt;

    public function buildProfiles(Profile ...$profiles): Profiles;
}