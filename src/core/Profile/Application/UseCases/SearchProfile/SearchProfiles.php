<?php

namespace Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profiles;

class SearchProfiles extends UseCasesService
{
    public function __construct(ProfileRepositoryContract $profileRepository)
    {
        parent::__construct($profileRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): ?Profiles
    {
        $this->validateRequest($request, SearchProfilesRequest::class);

        /* @var SearchProfilesRequest $request */
        return $this->profileRepository->getAll($request->filters());
    }
}
