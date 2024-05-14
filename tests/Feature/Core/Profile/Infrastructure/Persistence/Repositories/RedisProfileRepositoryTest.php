<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
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
}
