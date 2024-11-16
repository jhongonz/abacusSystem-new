<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Commands;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Infrastructure\Commands\ProfileWarmup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tests\TestCase;

#[CoversClass(ProfileWarmup::class)]
class ProfileWarmupTest extends TestCase
{
    private ProfileRepositoryContract|MockObject $readRepository;
    private LoggerInterface|MockObject $logger;
    private ProfileFactoryContract|MockObject $factory;
    private ProfileRepositoryContract|MockObject $writeRepository;
    private ProfileWarmup $command;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->readRepository = $this->createMock(ProfileRepositoryContract::class);
        $this->writeRepository = $this->createMock(ProfileRepositoryContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(ProfileFactoryContract::class);
        $this->command = new ProfileWarmup(
            $this->logger,
            $this->factory,
            $this->readRepository,
            $this->writeRepository
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->readRepository,
            $this->writeRepository,
            $this->factory,
            $this->logger,
            $this->command
        );
        parent::tearDown();
    }

    public function testNameAndDescriptionShouldReturnCorrect(): void
    {
        $this->assertSame('profile:warmup', $this->command->getName());
        $this->assertSame('Warmup profiles in memory', $this->command->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateProfileInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::once())
            ->method('getOption')
            ->willReturn(0);

        $profiles = $this->createMock(Profiles::class);
        $profiles->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->readRepository->expects(self::once())
            ->method('getAll')
            ->willReturn($profiles);

        $profileId = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with(1)
            ->willReturn($profileId);

        $profileMock = $this->createMock(Profile::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($profileId)
            ->willReturn($profileMock);

        $this->writeRepository->expects(self::once())
            ->method('persistProfile')
            ->with($profileMock)
            ->willReturn($profileMock);

        $this->logger->expects(self::once())
            ->method('info')
            ->with('Command executed');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::SUCCESS, $result);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldReturnExceptionProfileInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::once())
            ->method('getOption')
            ->willReturn(0);

        $profiles = $this->createMock(Profiles::class);
        $profiles->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->readRepository->expects(self::once())
            ->method('getAll')
            ->willReturn($profiles);

        $profileId = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with(1)
            ->willReturn($profileId);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($profileId)
            ->willThrowException(new ProfileNotFoundException('Profile not found'));

        $this->writeRepository->expects(self::never())
            ->method('persistProfile');

        $this->logger->expects(self::never())
            ->method('info');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Profile not found');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateProfileWithIdInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getOption')
            ->willReturn(1);

        $this->readRepository->expects(self::never())
            ->method('getAll');

        $profileId = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with(1)
            ->willReturn($profileId);

        $profileMock = $this->createMock(Profile::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($profileId)
            ->willReturn($profileMock);

        $this->writeRepository->expects(self::once())
            ->method('persistProfile')
            ->with($profileMock)
            ->willReturn($profileMock);

        $this->logger->expects(self::once())
            ->method('info')
            ->with('Command executed');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::SUCCESS, $result);
    }

    /**
     * @throws Exception
     */
    public function testHandleWithIdShouldReturnExceptionProfileInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getOption')
            ->willReturn(1);

        $this->readRepository->expects(self::never())
            ->method('getAll');

        $profileId = $this->createMock(ProfileId::class);
        $this->factory->expects(self::once())
            ->method('buildProfileId')
            ->with(1)
            ->willReturn($profileId);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($profileId)
            ->willThrowException(new ProfileNotFoundException('Profile not found'));

        $this->writeRepository->expects(self::never())
            ->method('persistProfile');

        $this->logger->expects(self::never())
            ->method('info');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Profile not found');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }
}
