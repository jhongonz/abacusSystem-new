<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\CreateUser;

use Core\User\Application\UseCases\CreateUser\CreateUser;
use Core\User\Application\UseCases\CreateUser\CreateUserRequest;
use Core\User\Application\UseCases\DeleteUser\DeleteUserRequest;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(CreateUser::class)]
class CreateUserTest extends TestCase
{
    private UserRepositoryContract|Mock $repositoryMock;
    private CreateUser $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(UserRepositoryContract::class);
        $this->useCase = new CreateUser($this->repositoryMock);
    }

    public function tearDown(): void
    {
        unset(
            $this->repositoryMock,
            $this->useCase
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_User_object(): void
    {
        $userDomainMock = $this->createMock(User::class);

        $requestMock = $this->createMock(CreateUserRequest::class);
        $requestMock->expects(self::once())
            ->method('user')
            ->willReturn($userDomainMock);

        $this->repositoryMock->expects(self::once())
            ->method('persistUser')
            ->with($userDomainMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(User::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(DeleteUserRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
