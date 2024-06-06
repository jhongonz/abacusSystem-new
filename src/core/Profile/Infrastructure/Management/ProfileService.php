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
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Exception;
use Psr\Log\LoggerInterface;

class ProfileService implements ProfileManagementContract
{
    private ProfileFactoryContract $profileFactory;
    private ModuleService $moduleService;
    private ModuleFactoryContract $moduleFactory;
    private SearchProfileById $searchProfileById;
    private SearchProfiles $searchProfiles;
    private UpdateProfile $updateProfile;
    private DeleteProfile $deleteProfile;
    private CreateProfile $createProfile;
    private LoggerInterface $logger;

    public function __construct(
        ProfileFactoryContract $profileFactory,
        ModuleService $moduleService,
        ModuleFactoryContract $moduleFactory,
        SearchProfileById $searchProfileById,
        SearchProfiles $searchProfiles,
        UpdateProfile $updateProfile,
        DeleteProfile $deleteProfile,
        CreateProfile $createProfile,
        LoggerInterface $logger,
    ) {
        $this->profileFactory = $profileFactory;
        $this->moduleService = $moduleService;
        $this->moduleFactory = $moduleFactory;
        $this->searchProfileById = $searchProfileById;
        $this->searchProfiles = $searchProfiles;
        $this->updateProfile = $updateProfile;
        $this->deleteProfile = $deleteProfile;
        $this->createProfile = $createProfile;
        $this->logger = $logger;
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
