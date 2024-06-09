<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 11:07:41
 */

namespace App\Http\Controllers\ActionExecutors\ModuleActions;

use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;

class UpdateModuleActionExecutor extends ModuleActionExecutor
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
        $route = $request->input('route');
        $this->validateRoute($route);

        $dataUpdate = [
            'name' => $request->input('name'),
            'route' => $route,
            'icon' => $request->input('icon'),
            'position' => $request->input('position'),
            'key' => $request->input('key'),
        ];

        $request->mergeIfMissing(['dataUpdate' => json_encode($dataUpdate)]);
        return $this->orchestratorHandler->handler('update-module', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'update-module-action';
    }
}
