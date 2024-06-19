<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 10:51:10
 */

namespace App\Http\Controllers\ActionExecutors\CampusActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Campus\Domain\Campus;
use Illuminate\Http\Request;

class UpdateCampusActionExecutor extends CampusActionExecutor
{
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        parent::__construct($orchestratorHandler);
    }

    /**
     * @param Request $request
     * @return Campus
     */
    public function invoke(Request $request): Campus
    {
        $dataUpdate = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
        ];

        $request->merge(['dataUpdate' => json_encode($dataUpdate)]);
        return $this->orchestratorHandler->handler('update-campus', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'update-campus-action';
    }
}
