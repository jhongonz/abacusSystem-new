<?php

namespace Tests\Feature\Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\CreateUser\CreateUserRequest;
use Core\User\Application\UseCases\UpdateUser\UpdateUser;
use Core\User\Application\UseCases\UpdateUser\UpdateUserRequest;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\User\Application\UseCases\UpdateUser\DataProvider\DataProviderUpdateUser;
use Tests\TestCase;

#[CoversClass(UpdateUser::class)]
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
        $this->repository =  $this->createMock(UserRepositoryContract::class);
        $this->useCase = new UpdateUser($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderUpdateUser::class,'provider')]
    public function test_execute_should_return_user_object(array $dataUpdate): void
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
    public function test_execute_request_fail_should_return_exception(): void
    {
        $requestMock = $this->createMock(CreateUserRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
