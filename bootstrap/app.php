<?php

use App\Http\Middleware\OnlyAjaxRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$application = Application::configure(basePath: dirname(__DIR__));

$application->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
);

$application->withMiddleware(function (Middleware $middleware) {
    $middleware->redirectGuestsTo('/login');

    $middleware->alias([
        'only.ajax-request' => OnlyAjaxRequest::class,
    ]);
});

$application->withExceptions(function (Exceptions $exceptions) {
    //
});

$application->withCommands([
    //__DIR__.'/../src/core/Profile/Infrastructure/Commands',
    \Core\Profile\Infrastructure\Commands\ModuleWarmup::class,
    \Core\Profile\Infrastructure\Commands\ProfileWarmup::class,
    \Core\Employee\Infrastructure\Commands\EmployeeWarmup::class,
]);

return $application->create();
