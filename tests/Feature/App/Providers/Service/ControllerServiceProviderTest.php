<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-19 23:01:39
 */

namespace Tests\Feature\App\Providers\Service;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandlerContract;
use App\Http\Controllers\ActionExecutors\EmployeeActions\CreateEmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\EmployeeActions\UpdateEmployeeActionExecutor;
use App\Providers\Service\ControllerServiceProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(ControllerServiceProvider::class)]
class ControllerServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(ControllerServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testBindsActionExecutorHandlerCorrectly(): void
    {
        $createEmployeeAction = $this->createMock(CreateEmployeeActionExecutor::class);
        $this->app->singleton(CreateEmployeeActionExecutor::class, function () use ($createEmployeeAction) {
            return $createEmployeeAction;
        });

        $updateEmployeeAction = $this->createMock(UpdateEmployeeActionExecutor::class);
        $this->app->singleton(UpdateEmployeeActionExecutor::class, function () use ($updateEmployeeAction) {
            return $updateEmployeeAction;
        });

        $instance = $this->app->make(ActionExecutorHandlerContract::class);

        $this->assertInstanceOf(ActionExecutorHandler::class, $instance);
    }

    /**
     * @throws Exception
     * @throws BindingResolutionException
     */
    public function testBootShouldAddActionExecutorCorrectly(): void
    {
        $handlerMock = $this->createMock(ActionExecutorHandler::class);
        $handlerMock->expects(self::exactly(2))
            ->method('addActionExecutor')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->app->singleton(ActionExecutorHandlerContract::class, function () use ($handlerMock) {
            return $handlerMock;
        });

        $serviceProvider = new ControllerServiceProvider($this->app);
        $serviceProvider->boot();
    }

    public function testProvidesShouldReturnArrayCorrectly(): void
    {
        $serviceProvider = new ControllerServiceProvider($this->app);
        $provides = $serviceProvider->provides();

        $dataExpected = [
            ActionExecutorHandlerContract::class,
        ];
        $this->assertIsArray($provides);
        $this->assertEquals($dataExpected, $provides);
    }
}
