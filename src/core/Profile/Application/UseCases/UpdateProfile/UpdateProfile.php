<?php

namespace Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Exception;

class UpdateProfile extends UseCasesService
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
        $this->validateRequest($request, UpdateProfileRequest::class);

        $profile = $this->profileRepository->find($request->profileId());
        foreach ($request->data() as $field => $value) {
            $methodName = 'change'.\ucfirst($field);

            if (is_callable([$this, $methodName])) {
                $profile = $this->{$methodName}($profile, $value);
            }
        }

        $profile->refreshSearch();
        return $this->profileRepository->persistProfile($profile);
    }

    /**
     * @throws Exception
     */
    private function changeState(Profile $profile, int $value): Profile
    {
        $profile->state()->setValue($value);
        return $profile;
    }

    private function changeDescription(Profile $profile, string $value): Profile
    {
        $profile->description()->setValue($value);
        return $profile;
    }

    private function changeName(Profile $profile, string $value): Profile
    {
        $profile->name()->setValue($value);
        return $profile;
    }

    private function changeModules(Profile $profile, array $modules): Profile
    {
        $profile->setModulesAggregator($modules);
        return $profile;
    }
}
