<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-19 20:34:23
 */

namespace Tests\Feature\App\Providers;

use App\Providers\ViewFactoryServiceProvider;
use App\View\Composers\EventAjaxComposer;
use App\View\Composers\HomeComposer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ViewFactoryServiceProvider::class)]
class ViewFactoryServiceProviderTest extends TestCase
{
    private Application|MockObject $application;
    private Factory|MockObject $viewFactory;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->application = $this->createMock(Application::class);
        $this->viewFactory = $this->createMock(Factory::class);

        $this->application->expects(self::once())
            ->method('make')
            ->with(Factory::class)
            ->willReturn($this->viewFactory);
    }

    protected function tearDown(): void
    {
        unset(
            $this->application,
            $this->viewFactory
        );
        parent::tearDown();
    }

    /**
     * @throws BindingResolutionException
     */
    public function testViewComposersAreRegistered(): void
    {
        $callIndex = 0;
        $this->viewFactory->expects(self::exactly(2))
            ->method('composer')
            ->willReturnCallback(function (string $view, string $callback) use (&$callIndex): array {
                if (0 === $callIndex) {
                    $this->assertIsString($callback);
                    $this->assertEquals('layouts.home', $view);
                    $this->assertEquals(HomeComposer::class, $callback);
                } elseif (1 === $callIndex) {
                    $this->assertIsString($callback);
                    $this->assertEquals('*', $view);
                    $this->assertEquals(EventAjaxComposer::class, $callback);
                }

                ++$callIndex;

                return [];
            });

        $provider = new ViewFactoryServiceProvider($this->application);
        $provider->boot();
    }
}
