<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Commands;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Infrastructure\Commands\CampusWarmup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tests\TestCase;

#[CoversClass(CampusWarmup::class)]
class CampusWarmupTest extends TestCase
{
    private LoggerInterface|MockObject $logger;
    private CampusFactoryContract|MockObject $campusFactory;
    private CampusRepositoryContract|MockObject $readRepository;
    private CampusRepositoryContract|MockObject $writeRepository;
    private CampusWarmup|MockObject $command;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->campusFactory = $this->createMock(CampusFactoryContract::class);
        $this->readRepository = $this->createMock(CampusRepositoryContract::class);
        $this->writeRepository = $this->createMock(CampusRepositoryContract::class);

        $this->command = $this->getMockBuilder(CampusWarmup::class)
            ->setConstructorArgs([
                $this->logger,
                $this->campusFactory,
                $this->readRepository,
                $this->writeRepository,
            ])
            ->onlyMethods(['info'])
            ->getMock();
    }

    public function tearDown(): void
    {
        unset(
            $this->logger,
            $this->campusFactory,
            $this->readRepository,
            $this->writeRepository
        );
        parent::tearDown();
    }

    public function testNameAndDescriptionShouldReturnCorrect(): void
    {
        $this->assertSame('campus:warmup', $this->command->getName());
        $this->assertSame('Warmup campus in memory redis', $this->command->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateCampusInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getArgument')
            ->with('id')
            ->willReturn('1');

        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturnCallback(function ($id) use ($campusIdMock) {
                $this->assertIsInt($id);

                return $campusIdMock;
            });

        $campusMock = $this->createMock(Campus::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn($campusMock);

        $this->writeRepository->expects(self::once())
            ->method('persistCampus')
            ->with($campusMock)
            ->willReturn($campusMock);

        $this->command->expects(self::once())
            ->method('info')
            ->with('Campus command executed');

        $this->logger->expects(self::once())
            ->method('info')
            ->with('Campus command executed');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::SUCCESS, $result);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldReturnException(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getArgument')
            ->with('id')
            ->willReturn('1');

        $campusIdMock = $this->createMock(CampusId::class);
        $this->campusFactory->expects(self::once())
            ->method('buildCampusId')
            ->with(1)
            ->willReturnCallback(function ($id) use ($campusIdMock) {
                $this->assertIsInt($id);

                return $campusIdMock;
            });

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willThrowException(new \Exception('Campus not found'));

        $this->writeRepository->expects(self::never())
            ->method('persistCampus');

        $this->logger->expects(self::never())
            ->method('info');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Campus not found');

        $this->command->expects(self::never())
            ->method('info');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }
}
