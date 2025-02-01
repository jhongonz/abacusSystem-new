<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-17 19:41:14
 */

namespace Tests\Feature\App\Providers;

use App\Providers\AppServiceProvider;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Container\BindingResolutionException;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(AppServiceProvider::class)]
class AppServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(AppServiceProvider::class);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBindsImageManagerInterfaceCorrectly(): void
    {
        $driverMock = $this->createMock(Driver::class);
        $this->app->singleton(Driver::class, function () use ($driverMock) {
            return $driverMock;
        });

        $instance = $this->app->make(ImageManagerInterface::class);

        $this->assertInstanceOf(ImageManager::class, $instance);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBindsStatefulGuardCorrectly(): void
    {
        $statefulGuardMock = $this->createMock(StatefulGuard::class);

        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->expects(self::once())
            ->method('guard')
            ->willReturn($statefulGuardMock);

        $this->app->singleton(AuthManager::class, function () use ($authManagerMock) {
            return $authManagerMock;
        });

        $instance = $this->app->make(StatefulGuard::class);

        $this->assertInstanceOf(StatefulGuard::class, $instance);
        $this->assertEquals($statefulGuardMock, $instance);
    }
}
