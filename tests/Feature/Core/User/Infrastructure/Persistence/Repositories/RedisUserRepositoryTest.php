<?php

namespace Tests\Feature\Core\User\Infrastructure\Persistence\Repositories;

use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Exceptions\UserPersistException;
use Core\User\Infrastructure\Persistence\Repositories\RedisUserRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(RedisUserRepository::class)]
class RedisUserRepositoryTest extends TestCase
{
    private UserFactoryContract|MockObject $userFactory;
    private UserDataTransformerContract|MockObject $dataTransformer;
    private LoggerInterface|MockObject $logger;
    private string $keyPrefix;
    private int $priority;
    private RedisUserRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->priority = 100;
        $this->keyPrefix = 'user';
        $this->userFactory = $this->createMock(UserFactoryContract::class);
        $this->dataTransformer = $this->createMock(UserDataTransformerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->repository = new RedisUserRepository(
            $this->userFactory,
            $this->dataTransformer,
            $this->logger,
            $this->keyPrefix,
            $this->priority
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->userFactory,
            $this->dataTransformer,
            $this->logger,
            $this->keyPrefix,
            $this->priority,
            $this->repository,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function test_findCriteria_should_return_user_object(): void
    {
        $key = 'user::login-test';
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login-test');

        Redis::shouldReceive('get')->once()
            ->with($key)
            ->andReturn('{}');

        $userMock = $this->createMock(User::class);
        $this->userFactory->expects(self::once())
            ->method('buildUserFromArray')
            ->with([])
            ->willReturn($userMock);

        $result = $this->repository->findCriteria($loginMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function test_findCriteria_should_return_null(): void
    {
        $key = 'user::login-test';
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login-test');

        Redis::shouldReceive('get')->once()
            ->with($key)
            ->andReturn(null);

        $this->userFactory->expects(self::never())
            ->method('buildUserFromArray');

        $result = $this->repository->findCriteria($loginMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function test_findCriteria_should_return_exception(): void
    {
        $key = 'user::login-test';
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn('login-test');

        Redis::shouldReceive('get')->once()
            ->with($key)
            ->andThrow(
                UserNotFoundException::class,
                'User not found by login login-test',
            );

        $this->logger->expects(self::once())
            ->method('error')
            ->with('User not found by login login-test');

        $this->userFactory->expects(self::never())
            ->method('buildUserFromArray');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found by login login-test');

        $this->repository->findCriteria($loginMock);
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function test_find_should_return_user_object(): void
    {
        $key = 'user::1';
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')->once()
            ->with($key)
            ->andReturn('{}');

        $userMock = $this->createMock(User::class);
        $this->userFactory->expects(self::once())
            ->method('buildUserFromArray')
            ->with([])
            ->willReturn($userMock);

        $result = $this->repository->find($userIdMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function test_find_should_return_null(): void
    {
        $key = 'user::1';
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')->once()
            ->with($key)
            ->andReturn(null);

        $this->userFactory->expects(self::never())
            ->method('buildUserFromArray');

        $result = $this->repository->find($userIdMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function test_find_should_return_exception(): void
    {
        $key = 'user::1';
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')->once()
            ->with($key)
            ->andThrow(
                UserNotFoundException::class,
                'User not found by id 1',
            );

        $this->logger->expects(self::once())
            ->method('error')
            ->with('User not found by id 1');

        $this->userFactory->expects(self::never())
            ->method('buildUserFromArray');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found by id 1');

        $this->repository->find($userIdMock);
    }

    /**
     * @throws Exception
     * @throws UserPersistException
     */
    public function test_persistUser_should_return_user_object(): void
    {
        $loginKey = 'user::login-test';
        $userIdKey = 'user::1';

        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login-test');

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($loginMock);

        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($userMock)
            ->willReturnSelf();

        $userData = [];
        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn($userData);

        Redis::shouldReceive('set')
            ->once()
            ->with($loginKey, json_encode($userData))
            ->andReturnUndefined();

        Redis::shouldReceive('set')
            ->once()
            ->with($userIdKey, json_encode($userData))
            ->andReturnUndefined();

        $result = $this->repository->persistUser($userMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    /**
     * @throws Exception
     * @throws UserPersistException
     */
    public function test_persistUser_should_return_exception(): void
    {
        $loginKey = 'user::login-test';
        $userIdKey = 'user::1';

        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login-test');

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($loginMock);

        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($userMock)
            ->willReturnSelf();

        $userData = [];
        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn($userData);

        Redis::shouldReceive('set')
            ->once()
            ->with($loginKey, json_encode($userData))
            ->andThrow(
                UserPersistException::class,
                'It could not persist User with key '.$userIdKey.' in redis'
            );

        $this->logger->expects(self::once())
            ->method('error')
            ->with('It could not persist User with key '.$userIdKey.' in redis');

        $this->expectException(UserPersistException::class);
        $this->expectExceptionMessage('It could not persist User with key '.$userIdKey.' in redis');

        $this->repository->persistUser($userMock);
    }

    public function test_priority_should_return_int(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(100, $result);
    }

    public function test_changePriority_should_change_value_and_return_self(): void
    {
        $result = $this->repository->changePriority(150);
        $value = $result->priority();

        $this->assertInstanceOf(RedisUserRepository::class, $result);
        $this->assertSame($result, $this->repository);
        $this->assertIsInt($value);
        $this->assertSame(150, $value);
    }

    /**
     * @throws Exception
     */
    public function test_delete_should_delete_data_user(): void
    {
        $key = 'user::1';
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('delete')
            ->with($key)
            ->andReturnUndefined();

        $this->repository->delete($userIdMock);
    }
}
