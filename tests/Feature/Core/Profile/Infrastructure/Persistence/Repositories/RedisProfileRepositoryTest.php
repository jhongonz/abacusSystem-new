<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilePersistException;
use Core\Profile\Infrastructure\Persistence\Repositories\RedisProfileRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(RedisProfileRepository::class)]
class RedisProfileRepositoryTest extends TestCase
{
    private ProfileFactoryContract|MockObject $factory;
    private ProfileDataTransformerContract|MockObject $dataTransformer;
    private LoggerInterface|MockObject $logger;
    private RedisProfileRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(ProfileFactoryContract::class);
        $this->dataTransformer = $this->createMock(ProfileDataTransformerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->repository = new RedisProfileRepository(
            $this->factory,
            $this->dataTransformer,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->dataTransformer,
            $this->factory,
            $this->repository,
            $this->logger
        );
        parent::tearDown();
    }

    public function test_priority_should_return_int(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(100, $result);
    }

    public function test_changePriority_should_return_self(): void
    {
        $result = $this->repository->changePriority(50);

        $this->assertInstanceOf(RedisProfileRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(50, $result->priority());
    }

    public function test_getAll_should_return_null(): void
    {
        $result = $this->repository->getAll();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_persistProfiles_should_return_object(): void
    {
        $profiles = $this->createMock(Profiles::class);
        $result = $this->repository->persistProfiles($profiles);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($profiles, $result);
    }

    /**
     * @throws Exception
     */
    public function test_deleteProfile_should_return_void(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('delete')
            ->once()
            ->with('profile::1')
            ->andReturnUndefined();

        $this->repository->deleteProfile($profileId);
        $this->assertTrue(true);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function test_find_should_return_object(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('profile::1')
            ->andReturn('{}');

        $profile = $this->createMock(Profile::class);
        $this->factory->expects(self::once())
            ->method('buildProfileFromArray')
            ->with([])
            ->willReturn($profile);

        $result = $this->repository->find($profileId);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profile, $result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function test_find_should_return_null(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('profile::1')
            ->andReturn(null);

        $this->factory->expects(self::never())
            ->method('buildProfileFromArray');

        $result = $this->repository->find($profileId);

        $this->assertNull($result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function test_find_should_return_exception(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('profile::1')
            ->andThrow(\Exception::class, 'testing');

        $this->factory->expects(self::never())
            ->method('buildProfileFromArray');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found by id 1');

        $this->repository->find($profileId);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function test_findCriteria_should_return_object(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::once())
            ->method('value')
            ->willReturn('Jame');

        Redis::shouldReceive('get')
            ->once()
            ->with('profile::Jame')
            ->andReturn('{}');

        $profile = $this->createMock(Profile::class);
        $this->factory->expects(self::once())
            ->method('buildProfileFromArray')
            ->with([])
            ->willReturn($profile);

        $result = $this->repository->findCriteria($profileName);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profile, $result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function test_findCriteria_should_return_null(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::once())
            ->method('value')
            ->willReturn('Jame');

        Redis::shouldReceive('get')
            ->once()
            ->with('profile::Jame')
            ->andReturn(null);

        $this->factory->expects(self::never())
            ->method('buildProfileFromArray');

        $result = $this->repository->findCriteria($profileName);

        $this->assertNull($result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws Exception
     */
    public function test_findCriteria_should_return_exception(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::exactly(2))
            ->method('value')
            ->willReturn('Jame');

        Redis::shouldReceive('get')
            ->once()
            ->with('profile::Jame')
            ->andThrow(\Exception::class, 'testing');

        $this->factory->expects(self::never())
            ->method('buildProfileFromArray');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found by name Jame');

        $this->repository->findCriteria($profileName);
    }

    /**
     * @throws ProfilePersistException
     * @throws Exception
     */
    public function test_persistProfile_should_return_object(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $profileNameMock = $this->createMock(ProfileName::class);
        $profileNameMock->expects(self::once())
            ->method('value')
            ->willReturn('Jame');

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $profileMock->expects(self::once())
            ->method('name')
            ->willReturn($profileNameMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($profileMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('profile::1', '[]')
            ->andReturnUndefined();

        Redis::shouldReceive('set')
            ->once()
            ->with('profile::Jame', '[]')
            ->andReturnUndefined();

        $result = $this->repository->persistProfile($profileMock);

        $this->assertInstanceOf(Profile::class, $profileMock);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws ProfilePersistException
     * @throws Exception
     */
    public function test_persistProfile_should_return_exception(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $profileNameMock = $this->createMock(ProfileName::class);
        $profileNameMock->expects(self::once())
            ->method('value')
            ->willReturn('Jame');

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $profileMock->expects(self::once())
            ->method('name')
            ->willReturn($profileNameMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($profileMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('profile::1', '[]')
            ->andReturnUndefined();

        Redis::shouldReceive('set')
            ->once()
            ->with('profile::Jame', '[]')
            ->andThrow(\Exception::class, 'testing');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(ProfilePersistException::class);
        $this->expectExceptionMessage('It could not persist Profile with key profile::1 in redis');

        $this->repository->persistProfile($profileMock);
    }
}
