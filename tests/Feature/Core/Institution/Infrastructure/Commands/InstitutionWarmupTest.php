<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Commands;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Exceptions\InstitutionNotFoundException;
use Core\Institution\Infrastructure\Commands\InstitutionWarmup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tests\TestCase;

#[CoversClass(InstitutionWarmup::class)]
class InstitutionWarmupTest extends TestCase
{
    private LoggerInterface|MockObject $logger;
    private InstitutionFactoryContract|MockObject $factory;
    private InstitutionRepositoryContract|MockObject $readRepository;
    private InstitutionRepositoryContract|MockObject $writeRepository;
    private InstitutionWarmup|MockObject $command;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(InstitutionFactoryContract::class);
        $this->readRepository = $this->createMock(InstitutionRepositoryContract::class);
        $this->writeRepository = $this->createMock(InstitutionRepositoryContract::class);

        $this->command = $this->getMockBuilder(InstitutionWarmup::class)
            ->setConstructorArgs([
                $this->logger,
                $this->factory,
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
            $this->factory,
            $this->readRepository,
            $this->writeRepository,
            $this->command
        );
        parent::tearDown();
    }

    public function testNameAndDescriptionShouldReturnCorrect(): void
    {
        $this->assertSame('institution:warmup', $this->command->getName());
        $this->assertSame('Warmup institution in memory', $this->command->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateInstitutionInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getArgument')
            ->with('id')
            ->willReturn(1);

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionId')
            ->with(1)
            ->willReturn($institutionIdMock);

        $institutionMock = $this->createMock(Institution::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($institutionIdMock)
            ->willReturn($institutionMock);

        $this->writeRepository->expects(self::once())
            ->method('persistInstitution')
            ->with($institutionMock)
            ->willReturn($institutionMock);

        $this->command->expects(self::once())
            ->method('info')
            ->with('Institution command executed');

        $this->logger->expects(self::once())
            ->method('info')
            ->with('Institution command executed');

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
            ->willReturn(1);

        $institutionIdMock = $this->createMock(InstitutionId::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionId')
            ->with(1)
            ->willReturn($institutionIdMock);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($institutionIdMock)
            ->willThrowException(new InstitutionNotFoundException('Institution not found with id: 1'));

        $this->writeRepository->expects(self::never())
            ->method('persistInstitution');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Institution not found with id: 1');

        $this->command->expects(self::never())
            ->method('info');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }
}
