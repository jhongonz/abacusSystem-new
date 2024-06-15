<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 06:54:34
 */

namespace App\Http\Controllers\ActionExecutors\ProfileActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class UpdateProfileActionExecutor extends ProfileActionExecutor
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
        $dataUpdate = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'modules' => $modulesAggregator,
        ];

        $request->merge(['dataUpdate' => json_encode($dataUpdate)]);
        return $this->orchestratorHandler->handler('update-profile', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'update-profile-action';
    }
}
