<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-17 22:14:56
 */

namespace Tests\Feature\Core\User\Infrastructure\Persistence\Repositories;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Persistence\Repositories\ChainUserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainUserRepository::class)]
class ChainUserRepositoryTest extends TestCase
{
    private ChainUserRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainUserRepository::class)
            ->onlyMethods(['read', 'readFromRepositories', 'write'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testFunctionNamePersistShouldReturnString(): void
    {
        $result = $this->repository->functionNamePersist();

        $this->assertIsString($result);
        $this->assertEquals('persistUser', $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnValueObject(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userMock = $this->createMock(User::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $userIdMock)
            ->willReturn($userMock);

        $result = $this->repository->find($userIdMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnNull(): void
    {
        $userIdMock = $this->createMock(UserId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $userIdMock)
            ->willReturn(null);

        $result = $this->repository->find($userIdMock);

        $this->assertNotInstanceOf(User::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnException(): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $userIdMock)
            ->willThrowException(new \Exception());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found by id 1');

        $this->repository->find($userIdMock);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindCriteriaShouldReturnValueObject(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $userMock = $this->createMock(User::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $loginMock)
            ->willReturn($userMock);

        $result = $this->repository->findCriteria($loginMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindCriteriaShouldReturnNull(): void
    {
        $loginMock = $this->createMock(UserLogin::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $loginMock)
            ->willReturn(null);

        $result = $this->repository->findCriteria($loginMock);

        $this->assertNotInstanceOf(User::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindCriteriaShouldReturnException(): void
    {
        $loginMock = $this->createMock(UserLogin::class);
        $loginMock->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $loginMock)
            ->willThrowException(new \Exception());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found by login test');

        $this->repository->findCriteria($loginMock);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $userIdMock = $this->createMock(UserId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('delete', $userIdMock);

        $this->repository->delete($userIdMock);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPersistUserShouldReturnVoid(): void
    {
        $userMock = $this->createMock(User::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('persistUser', $userMock)
            ->willReturn($userMock);

        $result = $this->repository->persistUser($userMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }
}
