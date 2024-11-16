<?php

namespace Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;

class SearchProfileById extends UseCasesService
{
    public function __construct(ProfileRepositoryContract $profileRepository)
    {
        parent::__construct($profileRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): ?Profile
    {
        $this->validateRequest($request, SearchProfileByIdRequest::class);

        /** @var SearchProfileByIdRequest $request */
        return $this->profileRepository->find($request->profileId());
    }
}
