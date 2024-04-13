<?php

namespace Core\Profile\Application\UseCases\CreateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\Profile;

class CreateProfileRequest implements RequestService
{
    private Profile $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function profile(): Profile
    {
        return $this->profile;
    }
}
