<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Commands;

use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Infrastructure\Commands\ModuleWarmup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tests\TestCase;

#[CoversClass(ModuleWarmup::class)]
class ModuleWarmupTest extends TestCase
{
    private LoggerInterface|MockObject $logger;
    private ModuleFactoryContract|MockObject $factory;
    private ModuleRepositoryContract|MockObject $readRepository;
    private ModuleRepositoryContract|MockObject $writeRepository;
    private ModuleWarmup $command;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(ModuleFactoryContract::class);
        $this->readRepository = $this->createMock(ModuleRepositoryContract::class);
        $this->writeRepository = $this->createMock(ModuleRepositoryContract::class);

        $this->command = new ModuleWarmup(
            $this->logger,
            $this->factory,
            $this->readRepository,
            $this->writeRepository
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->logger,
            $this->factory,
            $this->readRepository,
            $this->writeRepository,
            $this->command
        );
        parent::tearDown();
    }

    public function testNameAndDescriptionShouldReturnCorrect(): void
    {
        $this->assertSame('module:warmup', $this->command->getName());
        $this->assertSame('Warmup modules in memory', $this->command->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateModuleInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::once())
            ->method('getOption')
            ->with('id')
            ->willReturn(0);

        $modulesMock = $this->createMock(Modules::class);
        $modulesMock->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->readRepository->expects(self::once())
            ->method('getAll')
            ->willReturn($modulesMock);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $moduleMock = $this->createMock(Module::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($moduleIdMock)
            ->willReturn($moduleMock);

        $this->writeRepository->expects(self::once())
            ->method('persistModule')
            ->with($moduleMock)
            ->willReturn($moduleMock);

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
    public function testHandleShouldReturnExceptionInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::once())
            ->method('getOption')
            ->with('id')
            ->willReturn(0);

        $modulesMock = $this->createMock(Modules::class);
        $modulesMock->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->readRepository->expects(self::once())
            ->method('getAll')
            ->willReturn($modulesMock);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($moduleIdMock)
            ->willThrowException(new ModuleNotFoundException('Module not found with id: 1'));

        $this->writeRepository->expects(self::never())
            ->method('persistModule');

        $this->logger->expects(self::never())
            ->method('info');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Module not found with id: 1');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateModuleWithOptionIdInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getOption')
            ->with('id')
            ->willReturn(1);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $moduleMock = $this->createMock(Module::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($moduleIdMock)
            ->willReturn($moduleMock);

        $this->writeRepository->expects(self::once())
            ->method('persistModule')
            ->with($moduleMock)
            ->willReturn($moduleMock);

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
    public function testHandleWithOptionIdShouldReturnExceptionInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getOption')
            ->with('id')
            ->willReturn(1);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($moduleIdMock)
            ->willThrowException(new ModuleNotFoundException('Module not found with id: 1'));

        $this->writeRepository->expects(self::never())
            ->method('persistModule');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Module not found with id: 1');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }
}
