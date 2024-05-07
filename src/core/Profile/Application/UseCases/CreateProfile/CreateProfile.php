<?php

namespace Core\Profile\Application\UseCases\CreateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Exception;

class CreateProfile extends UseCasesService
{
    public function __construct(ProfileRepositoryContract $profileRepository)
    {
        parent::__construct($profileRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): Profile
    {
        $this->validateRequest($request, CreateProfileRequest::class);

        /** @var Profile $profile */
        $profile = $request->profile();
        $profile->refreshSearch();

        return $this->profileRepository->persistProfile($profile);
    }
}
