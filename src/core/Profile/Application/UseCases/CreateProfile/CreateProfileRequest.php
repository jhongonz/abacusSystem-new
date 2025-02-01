<?php

namespace Core\Profile\Application\UseCases\CreateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\Profile;

class CreateProfileRequest implements RequestService
{
    public function __construct(
        private readonly Profile $profile,
    ) {
    }

    public function profile(): Profile
    {
        return $this->profile;
    }
}
