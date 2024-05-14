<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Management;

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
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Infrastructure\Management\ModuleService;
use Core\Profile\Infrastructure\Management\ProfileService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(ProfileService::class)]
class ProfileServiceTest extends TestCase
{
    private ProfileFactoryContract|MockObject $factory;
    private ModuleService|MockObject $moduleService;
    private ModuleFactoryContract|MockObject $moduleFactory;
    private SearchProfileById|MockObject $searchProfileById;
    private SearchProfiles|MockObject $searchProfiles;
    private UpdateProfile|MockObject $updateProfile;
    private DeleteProfile|MockObject $deleteProfile;
    private CreateProfile|MockObject $createProfile;
    private LoggerInterface|MockObject $logger;
    private ProfileService $service;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(ProfileFactoryContract::class);
        $this->moduleService = $this->createMock(ModuleService::class);
        $this->moduleFactory = $this->createMock(ModuleFactoryContract::class);
        $this->searchProfileById = $this->createMock(SearchProfileById::class);
        $this->searchProfiles = $this->createMock(SearchProfiles::class);
        $this->updateProfile = $this->createMock(UpdateProfile::class);
        $this->deleteProfile = $this->createMock(DeleteProfile::class);
        $this->createProfile = $this->createMock(CreateProfile::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new ProfileService(
            $this->factory,
            $this->moduleService,
            $this->moduleFactory,
            $this->searchProfileById,
            $this->searchProfiles,
            $this->updateProfile,
            $this->deleteProfile,
            $this->createProfile,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->factory,
            $this->moduleService,
            $this->moduleFactory,
            $this->searchProfileById,
            $this->searchProfiles,
            $this->updateProfile,
            $this->deleteProfile,
            $this->createProfile,
            $this->logger,
            $this->service
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchProfileById_should_return_object(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $request = new SearchProfileByIdRequest($profileIdMock);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('modulesAggregator')
            ->willReturn([1]);

        $this->searchProfileById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($profileMock);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->moduleFactory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $moduleState = $this->createMock(ModuleState::class);
        $moduleState->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($moduleState);

        $this->moduleService->expects(self::once())
            ->method('searchModuleById')
            ->with($moduleIdMock)
            ->willReturn($moduleMock);

        $result = $this->service->searchProfileById($profileIdMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchProfileById_should_return_exception(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $request = new SearchProfileByIdRequest($profileIdMock);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('modulesAggregator')
            ->willReturn([1]);

        $this->searchProfileById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($profileMock);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->moduleFactory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $this->moduleService->expects(self::once())
            ->method('searchModuleById')
            ->with($moduleIdMock)
            ->willThrowException(new ModuleNotFoundException('Module not found with id 1'));

        $this->logger->expects(self::once())
            ->method('warning')
            ->with('Module not found with id 1');

        $result = $this->service->searchProfileById($profileIdMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchProfiles_should_return_object(): void
    {
        $filters = [];
        $request = new SearchProfilesRequest($filters);

        $profilesMock = $this->createMock(Profiles::class);
        $profilesMock->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->searchProfiles->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($profilesMock);

        $profileIdMock = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with(1)
            ->willReturn($profileIdMock);

        $profileMock = $this->createMock(Profile::class);
        $this->searchProfileById->expects(self::once())
            ->method('execute')
            ->willReturn($profileMock);

        $profilesMock->expects(self::once())
            ->method('addItem')
            ->with($profileMock)
            ->willReturnSelf();

        $result = $this->service->searchProfiles($filters);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($profilesMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_updateProfile_should_return_void(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $data = [];

        $request = new UpdateProfileRequest($profileIdMock, $data);

        $profileMock = $this->createMock(Profile::class);
        $this->updateProfile->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($profileMock);

        $this->service->updateProfile($profileIdMock, $data);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_deleteProfile_should_return_void(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $request = new DeleteProfileRequest($profileIdMock);

        $this->deleteProfile->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $this->service->deleteProfile($profileIdMock);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_createProfile_should_return_object(): void
    {
        $profileMock = $this->createMock(Profile::class);
        $request = new CreateProfileRequest($profileMock);

        $this->createProfile->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($profileMock);

        $this->service->createProfile($profileMock);
        $this->assertTrue(true);
    }
}
