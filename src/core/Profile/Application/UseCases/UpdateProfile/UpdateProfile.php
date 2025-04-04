<?php

namespace Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;

class UpdateProfile extends UseCasesService
{
    public function __construct(ProfileRepositoryContract $profileRepository)
    {
        parent::__construct($profileRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): Profile
    {
        $this->validateRequest($request, UpdateProfileRequest::class);

        /** @var UpdateProfileRequest $request */
        $profile = $this->profileRepository->find($request->profileId());

        foreach ($request->data() as $field => $value) {
            $methodName = $this->getFunctionName($field);

            if (is_callable([$this, $methodName])) {
                $profile = $this->{$methodName}($profile, $value);
            }
        }

        $profile->refreshSearch();

        return $this->profileRepository->persistProfile($profile);
    }

    protected function getFunctionName(string $field): string
    {
        return sprintf('change%s', ucfirst($field));
    }

    /**
     * @throws \Exception
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

    /**
     * @param array<int<0, max>, int|null> $modules
     */
    private function changeModules(Profile $profile, array $modules): Profile
    {
        $profile->setModulesAggregator($modules);

        return $profile;
    }

    private function changeUpdateAt(Profile $profile, \DateTime $dateTime): Profile
    {
        $profile->updatedAt()->setValue($dateTime);

        return $profile;
    }
}
