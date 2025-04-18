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
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\User\Infrastructure\Persistence\Repositories\DataProvider\DataProviderEloquentRepository;
use Tests\TestCase;

#[CoversClass(EloquentUserRepository::class)]
class EloquentUserRepositoryTest extends TestCase
{
    private DatabaseManager|MockInterface $database;

    private UserTranslator|MockObject $translator;

    private User|MockObject $model;

    private EloquentUserRepository $repository;

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
        $this->priority = 50;

        $this->repository = new EloquentUserRepository(
            $this->database,
            $this->translator,
            $this->model,
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
    public function testFindShouldReturnUserObject(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(100);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id', 100)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state', '>', -1)
            ->andReturnSelf();

        $modelMock = new \stdClass();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
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
    public function testFindShouldReturnException(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id', 1)
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state', '>', -1)
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
    public function testFindCriteriaShouldReturnUserObject(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_login', 'login')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state', '>', -1)
            ->andReturnSelf();

        $modelMock = new \stdClass();
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($modelMock);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->translator->expects(self::once())
            ->method('setModel')
            ->with($this->model)
            ->willReturnSelf();

        $userMock = $this->mock(UserDomain::class);
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
    public function testFindCriteriaShouldReturnException(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn('login');

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_login', 'login')
            ->andReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_state', '>', -1)
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

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertSame(50, $result);
        $this->assertIsInt($result);
    }

    public function testChangePriorityShouldReturnVoid(): void
    {
        $result = $this->repository->changePriority(70);
        $value = $result->priority();

        $this->assertInstanceOf(EloquentUserRepository::class, $result);
        $this->assertIsInt($value);
        $this->assertSame(70, $value);
    }

    /**
     * @param array<string, mixed> $dataInsert
     *
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderEloquentRepository::class, 'providerInsert')]
    public function testPersistUserShouldReturnUserObject(array $dataInsert, \DateTime $dateCreated): void
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
        $employeeId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::exactly(2))
            ->method('employeeId')
            ->willReturn($employeeId);
        $this->model->expects(self::once())
            ->method('changeEmployeeId')
            ->with(1)
            ->willReturnSelf();

        $profileId = $this->createMock(UserProfileId::class);
        $profileId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::exactly(2))
            ->method('profileId')
            ->willReturn($profileId);
        $this->model->expects(self::once())
            ->method('changeProfileId')
            ->with(1)
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

        $createdAtMock = $this->createMock(UserCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('value')
            ->willReturn($dateCreated);
        $userMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);
        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($dateCreated)
            ->willReturnSelf();

        $updatedAtMock = $this->createMock(UserUpdatedAt::class);
        $updatedAtMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($dateCreated);
        $userMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updatedAtMock);
        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($dateCreated)
            ->willReturnSelf();

        $userIdMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();
        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('users');
        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn($dataInsert);

        $this->model->expects(self::once())
            ->method('fill')
            ->with([])
            ->willReturnSelf();

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id', null)
            ->andReturnSelf();

        $objectMock = new \stdClass();
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($objectMock);

        $this->database->shouldReceive('table')
            ->times(2)
            ->with('users')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(null);

        $builderMock->shouldReceive('insertGetId')
            ->once()
            ->withAnyArgs()
            ->andReturn(2);

        $result = $this->repository->persistUser($userMock);

        $this->assertInstanceOf(UserDomain::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @param array<string, mixed> $dataReturn
     * @param array<string, mixed> $dataUpdate
     *
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderEloquentRepository::class, 'providerUpdate')]
    public function testPersistUserShouldUpdateModelAndReturnUserObject(array $dataReturn, array $dataUpdate, \DateTime $dateUpdated): void
    {
        $userMock = $this->createMock(UserDomain::class);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(10);

        $userMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($userIdMock);

        $this->model->expects(self::once())
            ->method('changeId')
            ->with(10)
            ->willReturnSelf();

        $employeeId = $this->createMock(UserEmployeeId::class);
        $employeeId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(5);

        $userMock->expects(self::exactly(2))
            ->method('employeeId')
            ->willReturn($employeeId);

        $this->model->expects(self::once())
            ->method('changeEmployeeId')
            ->with(5)
            ->willReturnSelf();

        $profileId = $this->createMock(UserProfileId::class);
        $profileId->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $userMock->expects(self::exactly(2))
            ->method('profileId')
            ->willReturn($profileId);

        $this->model->expects(self::once())
            ->method('changeProfileId')
            ->with(1)
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
            ->willReturn(2);

        $userMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->model->expects(self::once())
            ->method('changeState')
            ->with(2)
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

        $createdAtMock = $this->createMock(UserCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('value')
            ->willReturn($dateUpdated);

        $userMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $this->model->expects(self::once())
            ->method('changeCreatedAt')
            ->with($dateUpdated)
            ->willReturnSelf();

        $updatedAtMock = $this->createMock(UserUpdatedAt::class);
        $updatedAtMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($dateUpdated);

        $userMock->expects(self::exactly(2))
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $this->model->expects(self::once())
            ->method('changeUpdatedAt')
            ->with($dateUpdated)
            ->willReturnSelf();

        $this->model->expects(self::exactly(2))
            ->method('getTable')
            ->willReturn('users');

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn($dataUpdate);

        $this->model->expects(self::once())
            ->method('fill')
            ->with($dataReturn)
            ->willReturnSelf();

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($dataReturn);

        $this->database->shouldReceive('table')
            ->times(2)
            ->with('users')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(10);

        $builderMock->shouldReceive('where')
            ->times(2)
            ->with('user_id', 10)
            ->andReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->withAnyArgs()
            ->andReturn(2);

        $result = $this->repository->persistUser($userMock);

        $this->assertInstanceOf(UserDomain::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @throws Exception
     * @throws UserNotFoundException
     */
    public function testDeleteShouldDeleteRow(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(7);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $objectMock = new \stdClass();
        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn($objectMock);

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->model->expects(self::once())
            ->method('changeState')
            ->with(-1)
            ->willReturnSelf();

        $this->model->expects(self::once())
            ->method('changeDeletedAt')
            ->withAnyParameters()
            ->willReturnSelf();

        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id', 7)
            ->andReturnSelf();

        $builderMock->shouldReceive('update')
            ->once()
            ->with([])
            ->andReturnSelf();

        $this->model->expects(self::once())
            ->method('toArray')
            ->willReturn([]);

        $this->repository->delete($userIdMock);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function testDeleteUserNullShouldReturnException(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(7);

        $builderMock = $this->mock(Builder::class);
        $builderMock->shouldReceive('where')
            ->once()
            ->with('user_id', 7)
            ->andReturnSelf();

        $builderMock->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->model->expects(self::once())
            ->method('getTable')
            ->willReturn('users');

        $this->database->shouldReceive('table')
            ->once()
            ->with('users')
            ->andReturn($builderMock);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found with id: 7');

        $this->repository->delete($userIdMock);
    }
}
