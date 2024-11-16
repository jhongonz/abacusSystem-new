<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\SearchUser\SearchUserByIdRequest;
use Core\User\Application\UseCases\SearchUser\SearchUserByLogin;
use Core\User\Application\UseCases\SearchUser\SearchUserByLoginRequest;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserLogin;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(SearchUserByLogin::class)]
#[CoversClass(UseCasesService::class)]
class SearchUserByLoginTest extends TestCase
{
    private UserRepositoryContract|Mock $repository;

    private SearchUserByLogin $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryContract::class);
        $this->useCase = new SearchUserByLogin($this->repository);
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
    public function testExecuteShouldReturnUserObject(): void
    {
        $userMock = $this->createMock(User::class);
        $userLoginMock = $this->createMock(UserLogin::class);

        $requestMock = $this->createMock(SearchUserByLoginRequest::class);
        $requestMock->expects(self::once())
            ->method('login')
            ->willReturn($userLoginMock);

        $this->repository->expects(self::once())
            ->method('findCriteria')
            ->with($userLoginMock)
            ->willReturn($userMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnNull(): void
    {
        $userLoginMock = $this->createMock(UserLogin::class);

        $requestMock = $this->createMock(SearchUserByLoginRequest::class);
        $requestMock->expects(self::once())
            ->method('login')
            ->willReturn($userLoginMock);

        $this->repository->expects(self::once())
            ->method('findCriteria')
            ->with($userLoginMock)
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);

        $this->assertNull($result);
        $this->assertSame(null, $result);
    }

    /**
     * @throws Exception
     */
    public function testExecuteRequestFailShouldReturnException(): void
    {
        $requestMock = $this->createMock(SearchUserByIdRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
