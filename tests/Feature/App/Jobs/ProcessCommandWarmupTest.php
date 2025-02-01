<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 15:57:03
 */

namespace Tests\Feature\App\Jobs;

use App\Jobs\CommandWarmup;
use App\Jobs\ProcessCommandWarmup;
use Illuminate\Contracts\Console\Kernel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Tests\TestCase;

#[CoversClass(ProcessCommandWarmup::class)]
#[CoversClass(CommandWarmup::class)]
class ProcessCommandWarmupTest extends TestCase
{
    /** @var string|array<string> */
    private string|array $command;
    private ProcessCommandWarmup $warmup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = 'testing-command';
        $this->warmup = new ProcessCommandWarmup($this->command);
    }

    protected function tearDown(): void
    {
        unset($this->command, $this->warmup);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testHandleCommandWarmup(): void
    {
        $kernelMock = $this->createMock(Kernel::class);
        $kernelMock->expects(self::once())
            ->method('call')
            ->with($this->command)
            ->willReturn(1);

        $this->app->instance(Kernel::class, $kernelMock);
        $this->warmup = new ProcessCommandWarmup($this->command);

        $this->warmup->handle();
    }

    /**
     * @throws Exception
     */
    public function testHandleCommandWarmupWithArray(): void
    {
        $this->command = ['testing-command-one', 'testing-command-two'];

        $kernelMock = $this->createMock(Kernel::class);

        $callIndex = 0;
        $kernelMock->expects(self::exactly(2))
            ->method('call')
            ->willReturnCallback(function (string $command, array $parameters = []) use (&$callIndex) {
                if (0 === $callIndex) {
                    $this->assertEquals('testing-command-one', $command);
                } else {
                    $this->assertEquals('testing-command-two', $command);
                }

                $this->assertEmpty($parameters);

                ++$callIndex;

                return 1;
            });

        $this->app->instance(Kernel::class, $kernelMock);
        $this->warmup = new ProcessCommandWarmup($this->command);

        $this->warmup->handle();
    }

    /**
     * @throws Exception
     */
    public function testHandleCommandWarmupWithException(): void
    {
        $exceptionMock = new CommandNotFoundException('Command not found');

        $kernelMock = $this->createMock(Kernel::class);
        $kernelMock->expects(self::once())
            ->method('call')
            ->with($this->command)
            ->willThrowException($exceptionMock);

        $this->app->instance(Kernel::class, $kernelMock);

        $warmup = $this->getMockBuilder(ProcessCommandWarmup::class)
            ->setConstructorArgs([$this->command])
            ->onlyMethods(['fail'])
            ->getMock();

        $warmup->expects(self::once())
            ->method('fail')
            ->with($exceptionMock);

        $warmup->handle();
    }
}
