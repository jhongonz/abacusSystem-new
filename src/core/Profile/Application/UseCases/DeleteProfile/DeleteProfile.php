<?php

namespace Core\Profile\Application\UseCases\DeleteProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Exception;

class DeleteProfile extends UseCasesService
{
    public function __construct(ProfileRepositoryContract $profileRepository)
    {
        parent::__construct($profileRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|Profile|Profiles
    {
        $this->validateRequest($request, DeleteProfileRequest::class);
        $this->profileRepository->deleteProfile($request->id());
        return null;
    }
}
