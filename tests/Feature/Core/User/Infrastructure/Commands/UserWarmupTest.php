<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Commands;

use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Commands\UserWarmup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tests\TestCase;

#[CoversClass(UserWarmup::class)]
class UserWarmupTest extends TestCase
{
    private LoggerInterface|MockObject $loggerMock;

    private UserFactoryContract|MockObject $userFactoryMock;

    /** @var array|UserRepositoryContract[] */
    private array $repositories;

    private UserRepositoryContract|MockObject $readRepository;

    private UserRepositoryContract $writeRepository;

    private UserWarmup $command;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->userFactoryMock = $this->createMock(UserFactoryContract::class);
        $this->readRepository = $this->createMock(UserRepositoryContract::class);
        $this->writeRepository = $this->createMock(UserRepositoryContract::class);
        $this->repositories = [$this->writeRepository];
        $this->command = new UserWarmup(
            $this->loggerMock,
            $this->userFactoryMock,
            $this->readRepository,
            $this->writeRepository
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->loggerMock,
            $this->userFactoryMock,
            $this->readRepository,
            $this->repositories,
            $this->command,
            $this->writeRepository,
        );
        parent::tearDown();
    }

    public function testNameAndDescriptionShouldReturnCorrect(): void
    {
        $this->assertSame('user:warmup', $this->command->getName());
        $this->assertSame('Warmup user in memory', $this->command->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldPersistUserInWriteRepository(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getArgument')
            ->with('id')
            ->willReturn(2);

        $userIdMock = $this->createMock(UserId::class);
        $userMock = $this->createMock(User::class);

        $this->userFactoryMock->expects(self::once())
            ->method('buildId')
            ->with(2)
            ->willReturn($userIdMock);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($userIdMock)
            ->willReturn($userMock);

        $this->writeRepository->expects(self::once())
            ->method('persistUser')
            ->with($userMock)
            ->willReturn($userMock);

        $this->loggerMock->expects(self::once())
            ->method('info')
            ->with('User command executed');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertSame($result, Command::SUCCESS);
        $this->assertIsInt($result);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldReturnException(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getArgument')
            ->with('id')
            ->willReturn(2);

        $userId = $this->createMock(UserId::class);

        $this->userFactoryMock->expects(self::once())
            ->method('buildId')
            ->with(2)
            ->willReturn($userId);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($userId)
            ->willThrowException(new UserNotFoundException('User not found with login: 2'));

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('User not found with login: 2');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertSame($result, Command::FAILURE);
        $this->assertIsInt($result);
    }
}
