<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Commands;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Infrastructure\Commands\EmployeeWarmup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Tests\TestCase;

#[CoversClass(EmployeeWarmup::class)]
class EmployeeWarmupTest extends TestCase
{
    private LoggerInterface|MockObject $logger;

    private EmployeeFactoryContract|MockObject $factory;

    private EmployeeRepositoryContract|MockObject $readRepository;

    private EmployeeRepositoryContract|MockObject $writeRepository;

    private EmployeeWarmup|MockObject $command;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = $this->createMock(EmployeeFactoryContract::class);
        $this->readRepository = $this->createMock(EmployeeRepositoryContract::class);
        $this->writeRepository = $this->createMock(EmployeeRepositoryContract::class);

        $this->command = $this->getMockBuilder(EmployeeWarmup::class)
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
            $this->repositories,
            $this->command,
            $this->writeRepository
        );
        parent::tearDown();
    }

    public function testNameAndDescriptionShouldReturnCorrect(): void
    {
        $this->assertSame('employee:warmup', $this->command->getName());
        $this->assertSame('Warmup employee in memory', $this->command->getDescription());
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldUpdateEmployeeInRepositories(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects(self::exactly(2))
            ->method('getArgument')
            ->with('id')
            ->willReturn(1);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeIdMock);

        $employeeMock = $this->createMock(Employee::class);
        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($employeeIdMock)
            ->willReturn($employeeMock);

        $this->writeRepository->expects(self::once())
            ->method('persistEmployee')
            ->with($employeeMock)
            ->willReturn($employeeMock);

        $this->command->expects(self::once())
            ->method('info')
            ->with('Employee command executed');

        $this->logger->expects(self::once())
            ->method('info')
            ->with('Employee command executed');

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

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeIdMock);

        $this->readRepository->expects(self::once())
            ->method('find')
            ->with($employeeIdMock)
            ->willThrowException(new EmployeeNotFoundException('Employee not found with id: 1'));

        $this->writeRepository->expects(self::never())
            ->method('persistEmployee');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Employee not found with id: 1');

        $this->command->expects(self::never())
            ->method('info');

        $this->command->setInput($inputMock);
        $result = $this->command->handle();

        $this->assertIsInt($result);
        $this->assertSame(Command::FAILURE, $result);
    }
}
