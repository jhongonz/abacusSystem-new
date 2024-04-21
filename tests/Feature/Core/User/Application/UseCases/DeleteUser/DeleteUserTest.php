<?php

namespace Tests\Feature\Core\User\Application\UseCases\DeleteUser;

use Core\User\Application\UseCases\DeleteUser\DeleteUser;
use Core\User\Application\UseCases\DeleteUser\DeleteUserRequest;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\ValueObjects\UserId;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(DeleteUser::class)]
class DeleteUserTest extends TestCase
{
    private UserRepositoryContract|Mock $repository;
    private DeleteUser $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryContract::class);
        $this->useCase = new DeleteUser($this->repository);
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
    public function test_execute_should_return_user_object(): void
    {
        $userId = $this->createMock(UserId::class);

        $requestMock = $this->createMock(DeleteUserRequest::class);
        $requestMock->expects(self::once())
            ->method('userId')
            ->willReturn($userId);

        $this->repository->expects(self::once())
            ->method('delete')
            ->with($userId);

        $result = $this->useCase->execute($requestMock);

        $this->assertNull($result);
    }
}
