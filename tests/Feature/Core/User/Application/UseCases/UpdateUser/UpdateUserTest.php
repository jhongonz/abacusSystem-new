<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\CreateUser\CreateUserRequest;
use Core\User\Application\UseCases\UpdateUser\UpdateUser;
use Core\User\Application\UseCases\UpdateUser\UpdateUserRequest;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserCreatedAt;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserPassword;
use Core\User\Domain\ValueObjects\UserPhoto;
use Core\User\Domain\ValueObjects\UserProfileId;
use Core\User\Domain\ValueObjects\UserState;
use Core\User\Domain\ValueObjects\UserUpdatedAt;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\User\Application\UseCases\UpdateUser\DataProvider\DataProviderUpdateUser;
use Tests\TestCase;

#[CoversClass(UpdateUser::class)]
#[CoversClass(UseCasesService::class)]
class UpdateUserTest extends TestCase
{
    private UserRepositoryContract|Mock $repository;

    private UpdateUser $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryContract::class);
        $this->useCase = new UpdateUser($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $dataUpdate
     *
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderUpdateUser::class, 'provider')]
    public function testExecuteShouldReturnUserObject(array $dataUpdate): void
    {
        $userIdMock = $this->createMock(UserId::class);

        $requestMock = $this->createMock(UpdateUserRequest::class);
        $requestMock->expects(self::once())
            ->method('userId')
            ->willReturn($userIdMock);

        $requestMock->expects(self::once())
            ->method('data')
            ->willReturn($dataUpdate);

        $userMock = $this->createMock(User::class);

        $employeeIdMock = $this->createMock(UserEmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['employeeId'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeIdMock);

        $profileIdMock = $this->createMock(UserProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['profileId'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileIdMock);

        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['login'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('login')
            ->willReturn($loginMock);

        $passwordMock = $this->createMock(UserPassword::class);
        $passwordMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['password'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('password')
            ->willReturn($passwordMock);

        $stateMock = $this->createMock(UserState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['state'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $createdAtMock = $this->createMock(UserCreatedAt::class);
        $createdAtMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['createdAt'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAtMock);

        $updatedAtMock = $this->createMock(UserUpdatedAt::class);
        $updatedAtMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['updatedAt'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAtMock);

        $imageMock = $this->createMock(UserPhoto::class);
        $imageMock->expects(self::once())
            ->method('setValue')
            ->with($dataUpdate['image'])
            ->willReturnSelf();
        $userMock->expects(self::once())
            ->method('photo')
            ->willReturn($imageMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($userIdMock)
            ->willReturn($userMock);

        $this->repository->expects(self::once())
            ->method('persistUser')
            ->with($userMock)
            ->willReturn($userMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $userMock);
    }

    /**
     * @throws Exception
     */
    public function testExecuteRequestFailShouldReturnException(): void
    {
        $requestMock = $this->createMock(CreateUserRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFunctionNameShouldReturnNameValid(): void
    {
        $reflection = new \ReflectionClass(UpdateUser::class);
        $method = $reflection->getMethod('getFunctionName');
        $this->assertTrue($method->isProtected());

        $result = $method->invoke($this->useCase, 'name');
        $this->assertIsString($result);
        $this->assertSame('changeName', $result);
    }
}
