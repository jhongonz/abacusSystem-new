<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Persistence\Repositories;

use Core\User\Domain\User as UserDomain;
use Core\User\Domain\ValueObjects\UserCreatedAt;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserPassword;
use Core\User\Domain\ValueObjects\UserPhoto;
use Core\User\Domain\ValueObjects\UserProfileId;
use Core\User\Domain\ValueObjects\UserState;
use Core\User\Domain\ValueObjects\UserUpdatedAt;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use Core\User\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(EloquentUserRepository::class)]
class EloquentUserRepositoryTest extends TestCase
{
    private DatabaseManager|MockInterface $database;
    private UserTranslator|MockObject $translator;
    private User|MockObject $model;
    private EloquentUserRepository $repository;
    private LoggerInterface|MockObject $logger;
    private int $priority;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->database = $this->mock(DatabaseManager::class);
        $this->translator = $this->createMock(UserTranslator::class);
        $this->model = $this->createMock(User::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->priority = 50;

        $this->repository = new EloquentUserRepository(
            $this->database,
            $this->translator,
            $this->model,
            $this->logger,
            $this->priority
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->database,
            $this->translator,
            $this->model,
            $this->priority,
            $this->repository,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_find_should_return_user_object(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(100);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id',100)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state','>',-1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(new \stdClass());

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $userModel = new User([]);
        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($userModel)
            ->willReturnSelf();

        $userMock = $this->createMock(UserDomain::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($userMock);

        $result = $this->repository->find($userIdMock);

        $this->assertInstanceOf(UserDomain::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_find_should_return_exception(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id',1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state','>',-1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found with id: 1');

        $this->repository->find($userIdMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_findCriteria_should_return_user_object(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_login','login')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state','>',-1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(new \stdClass());

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $userModel = new User([]);
        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($userModel)
            ->willReturnSelf();

        $userMock = $this->createMock(UserDomain::class);
        $this->translator->expects(self::once())
            ->method('toDomain')
            ->willReturn($userMock);

        $result = $this->repository->findCriteria($loginMock);

        $this->assertInstanceOf(UserDomain::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_findCriteria_should_return_exception(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn('login');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_login','login')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state','>',-1)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found with login: login');

        $this->repository->findCriteria($loginMock);
    }

    public function test_priority_should_return_int(): void
    {
        $result = $this->repository->priority();

        $this->assertSame(50, $result);
        $this->assertIsInt($result);
    }

    public function test_changePriority_should_return_void(): void
    {
        $result = $this->repository->changePriority(70);
        $value = $result->priority();

        $this->assertInstanceOf(EloquentUserRepository::class, $result);
        $this->assertIsInt($value);
        $this->assertSame(70, $value);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    /*public function test_persistUser_should_return_user_object(): void
    {
        $userMock = $this->createMock(UserDomain::class);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(null);

        $userMock->expects(self::exactly(3))
            ->method('id')
            ->willReturn($userIdMock);

        $this->model->expects(self::once())
            ->method('changeId')
            ->with(null)
            ->willReturnSelf();

        $employeeId = $this->createMock(UserEmployeeId::class);
        $employeeId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeId);

        $this->model->expects(self::once())
            ->method('changeEmployeeId')
            ->with(null)
            ->willReturnSelf();

        $profileId = $this->createMock(UserProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileId);

        $this->model->expects(self::once())
            ->method('changeProfileId')
            ->with(3)
            ->willReturnSelf();

        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');

        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($loginMock);

        $this->model->expects(self::once())
            ->method('changeLogin')
            ->with('login')
            ->willReturnSelf();

        $passwordMock = $this->createMock(UserPassword::class);
        $passwordMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');

        $userMock->expects(self::once())
            ->method('password')
            ->willReturn($passwordMock);

        $this->model->expects(self::once())
            ->method('changePassword')
            ->with('12345')
            ->willReturnSelf();

        $stateMock = $this->createMock(UserState::class);
        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $userMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->model->expects(self::once())
            ->method('changeState')
            ->with(1)
            ->willReturnSelf();

        $photoMock = $this->createMock(UserPhoto::class);
        $photoMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');

        $userMock->expects(self::once())
            ->method('photo')
            ->willReturn($photoMock);

        $this->model->expects(self::once())
            ->method('changePhoto')
            ->with('image.jpg')
            ->willReturnSelf();

        $datetime = new \DateTime();
        $createdAtMock = $this->createMock(UserCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);

        $userMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $updatedAtMock = $this->createMock(UserUpdatedAt::class);
        $updatedAtMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($datetime);

        $userMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($datetime)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('save')
            ->willReturn(false);

        $userIdMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('find')
            ->once()
            ->with(null)
            ->andReturn([]);

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $result = $this->repository->persistUser($userMock);

        $this->assertInstanceOf(UserDomain::class, $result);
        //$this->assertSame($result, $userMock);
    }*/
}
