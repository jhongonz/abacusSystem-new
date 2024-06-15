<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 06:58:14
 */

namespace App\Http\Controllers\ActionExecutors\ProfileActions;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @codeCoverageIgnore
 */
#[CoversClass(ProfileActionExecutor::class)]
abstract class ProfileActionExecutor implements ActionExecutor
{
    protected OrchestratorHandlerContract $orchestratorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        $this->orchestratorHandler = $orchestratorHandler;
    }

    protected function getModulesAggregator(Request $request): array
    {
        $modulesAggregator = [];
        foreach ($request->input('modules') as $item) {
            $modulesAggregator[] = $item['id'];
        }

        return $modulesAggregator;
    }
}
