<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-18 01:29:38
 */

namespace Tests\Feature\App\Providers;

use App\Providers\EventServiceProvider;
use Illuminate\Contracts\Config\Repository as Configuration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EventServiceProvider::class)]
class EventServiceProviderTest extends TestCase
{
    private Application|MockObject $application;
    private Configuration|MockObject $configuration;
    private Dispatcher|MockObject $dispatcher;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->application = $this->createMock(Application::class);
        $this->configuration = $this->createMock(Configuration::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->application->expects(self::exactly(2))
            ->method('make')
            ->willReturnCallback(function (string $class) {
                $result = null;
                if (Configuration::class === $class) {
                    $result = $this->configuration;
                } elseif (Dispatcher::class === $class) {
                    $result = $this->dispatcher;
                }

                return $result;
            });
    }

    protected function tearDown(): void
    {
        unset(
            $this->application,
            $this->configuration,
            $this->dispatcher,
        );
        parent::tearDown();
    }

    /**
     * @throws BindingResolutionException
     */
    public function testInitializesDependencyCorrectly(): void
    {
        $serviceProvider = new EventServiceProvider($this->application);
        $this->assertInstanceOf(EventServiceProvider::class, $serviceProvider);
    }

    /**
     * @throws BindingResolutionException
     */
    public function testRegistersEventListenersCorrectly(): void
    {
        $eventListenerMock = [
            'event1' => 'listener1',
            'event2' => 'listener2',
        ];

        $this->configuration->expects(self::once())
            ->method('get')
            ->with('events')
            ->willReturn($eventListenerMock);

        $callIndex = 0;
        $this->dispatcher->expects(self::exactly(2))
            ->method('listen')
            ->willReturnCallback(function ($event, $listener) use (&$callIndex) {
                if (0 === $callIndex) {
                    $this->assertEquals('event1', $event);
                    $this->assertEquals('listener1', $listener);
                } elseif (1 === $callIndex) {
                    $this->assertEquals('event2', $event);
                    $this->assertEquals('listener2', $listener);
                }

                ++$callIndex;
            });

        $serviceProvider = new EventServiceProvider($this->application);
        $serviceProvider->boot();
    }
}
