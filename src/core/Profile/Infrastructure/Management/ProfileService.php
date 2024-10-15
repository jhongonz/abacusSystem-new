<?php

namespace Core\Profile\Infrastructure\Management;

use Core\Profile\Application\UseCases\CreateProfile\CreateProfile;
use Core\Profile\Application\UseCases\CreateProfile\CreateProfileRequest;
use Core\Profile\Application\UseCases\DeleteProfile\DeleteProfile;
use Core\Profile\Application\UseCases\DeleteProfile\DeleteProfileRequest;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfileById;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfileByIdRequest;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfiles;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfilesRequest;
use Core\Profile\Application\UseCases\UpdateProfile\UpdateProfile;
use Core\Profile\Application\UseCases\UpdateProfile\UpdateProfileRequest;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Exception;
use Psr\Log\LoggerInterface;

class ProfileService implements ProfileManagementContract
{
    public function __construct(
        private readonly ProfileFactoryContract $profileFactory,
        private readonly ModuleService $moduleService,
        private readonly SearchProfileById $searchProfileById,
        private readonly SearchProfiles $searchProfiles,
        private readonly UpdateProfile $updateProfile,
        private readonly DeleteProfile $deleteProfile,
        private readonly CreateProfile $createProfile,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Exception
     */
    public function searchProfileById(?int $id): ?Profile
    {
        $request = new SearchProfileByIdRequest(
            $this->profileFactory->buildProfileId($id)
        );
        $profile = $this->searchProfileById->execute($request);

        $modules = new Modules;
        foreach ($profile->modulesAggregator() as $item) {
            try {
                $module = $this->moduleService->searchModuleById($item);

                if ($module->state()->isActivated()) {
                    $modules->addItem($module);
                }
            } catch (Exception $exception) {
                $this->logger->warning($exception->getMessage(), $exception->getTrace());
            }
        }
        $profile->setModules($modules);

        return $profile;
    }

    /**
     * @throws Exception
     */
    public function searchProfiles(array $filters = []): Profiles
    {
        $request = new SearchProfilesRequest($filters);

        $profiles = $this->searchProfiles->execute($request);
        foreach ($profiles->aggregator() as $item) {
            $profile = $this->searchProfileById($item);
            $profiles->addItem($profile);
        }

        return $profiles;
    }

    /**
     * @throws Exception
     */
    public function updateProfile(int $id, array $data): Profile
    {
        $request = new UpdateProfileRequest(
            $this->profileFactory->buildProfileId($id),
            $data
        );

        return $this->updateProfile->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteProfile(int $id): void
    {
        $request = new DeleteProfileRequest(
            $this->profileFactory->buildProfileId($id)
        );

        $this->deleteProfile->execute($request);
    }

    /**
     * @throws Exception
     */
    public function createProfile(array $data): Profile
    {
        $request = new CreateProfileRequest(
            $this->profileFactory->buildProfileFromArray($data)
        );

        return $this->createProfile->execute($request);
    }
}
