<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 22:13:09
 */

namespace Tests\Feature\App\Listener;

use App\Jobs\ProcessCommandWarmup;
use App\Listeners\WarmupListener;
use Illuminate\Contracts\Console\Kernel;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(WarmupListener::class)]
class WarmupListenerTest extends TestCase
{
    private WarmupListener|MockInterface $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = \Mockery::mock(WarmupListener::class)->makePartial();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        unset($this->listener);

        parent::tearDown();
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testCallCommandWarmupShouldReturnClass(): void
    {
        $kernelMock = $this->createMock(Kernel::class);
        $this->app->instance(Kernel::class, $kernelMock);

        $reflection = new \ReflectionClass($this->listener);
        $method = $reflection->getMethod('callCommandWarmup');

        $commandWarmup = $method->invoke($this->listener, 'test:command');
        $this->assertInstanceOf(ProcessCommandWarmup::class, $commandWarmup);
    }
}
