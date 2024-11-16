<?php

namespace Core\Profile\Application\UseCases\DeleteProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;

class DeleteProfile extends UseCasesService
{
    public function __construct(ProfileRepositoryContract $profileRepository)
    {
        parent::__construct($profileRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): null
    {
        $this->validateRequest($request, DeleteProfileRequest::class);

        /* @var DeleteProfileRequest $request */
        $this->profileRepository->deleteProfile($request->id());

        return null;
    }
}
