<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 06:48:17
 */

namespace App\Http\Controllers\ActionExecutors\ProfileActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class CreateProfileActionExecutor extends ProfileActionExecutor
{
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        parent::__construct($orchestratorHandler);
    }

    /**
     * @param Request $request
     * @return Profile
     */
    public function invoke(Request $request): Profile
    {
        $modulesAggregator = $this->getModulesAggregator($request);
        $request->merge(['modulesAggregator' => json_encode($modulesAggregator)]);

        return $this->orchestratorHandler->handler('create-profile', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'create-profile-action';
    }
}
