<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 11:02:19
 */

namespace App\Http\Controllers\ActionExecutors\ModuleActions;

use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;

class CreateModuleActionExecutor extends ModuleActionExecutor
{
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        Router $router,
        LoggerInterface $logger
    ) {
        parent::__construct($orchestratorHandler, $router, $logger);
    }

    /**
     * @param Request $request
     * @return Module
     * @throws RouteNotFoundException
     */
    public function invoke(Request $request): Module
    {
        $this->validateRoute($request->input('route'));

        return $this->orchestratorHandler->handler('create-module', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'create-module-action';
    }
}
