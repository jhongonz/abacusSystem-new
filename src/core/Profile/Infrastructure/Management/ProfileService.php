<?php

namespace Core\Profile\Infrastructure\Management;

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
use Core\Profile\Domain\ValueObjects\ProfileId;
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

    private LoggerInterface $logger;

    public function __construct(
        ProfileFactoryContract $profileFactory,
        ModuleService $moduleService,
        ModuleFactoryContract $moduleFactory,
        SearchProfileById $searchProfileById,
        SearchProfiles $searchProfiles,
        UpdateProfile $updateProfile,
        LoggerInterface $logger,
    ) {
        $this->profileFactory = $profileFactory;
        $this->moduleService = $moduleService;
        $this->moduleFactory = $moduleFactory;
        $this->searchProfileById = $searchProfileById;
        $this->searchProfiles = $searchProfiles;
        $this->updateProfile = $updateProfile;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    public function searchProfileById(ProfileId $id): null|Profile
    {
        $request = new SearchProfileByIdRequest($id);
        $profile = $this->searchProfileById->execute($request);

        $modules = new Modules();
        foreach ($profile->modulesAggregator() as $item) {
            try {
                $moduleId = $this->moduleFactory->buildModuleId($item);
                $module = $this->moduleService->searchModuleById($moduleId);

                if ($module->state()->isActived()) {
                    $modules->addItem($module);
                }
            } catch (Exception $exception) {
                $this->logger->warning($exception->getMessage());
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
            $profile = $this->searchProfileById($this->profileFactory->buildProfileId($item));
            $profiles->addItem($profile);
        }

        return $profiles;
    }

    /**
     * @throws Exception
     */
    public function updateProfile(ProfileId $id, array $data): void
    {
        $request = new UpdateProfileRequest($id, $data);

        $this->updateProfile->execute($request);
    }
}
