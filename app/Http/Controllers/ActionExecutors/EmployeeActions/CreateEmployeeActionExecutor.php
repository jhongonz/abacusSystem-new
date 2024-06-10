<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 21:46:50
 */

namespace App\Http\Controllers\ActionExecutors\EmployeeActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Employee\Domain\Employee;
use Core\User\Domain\User;
use Illuminate\Http\Request;

class CreateEmployeeActionExecutor extends EmployeeActionExecutor
{
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
    ) {
        parent::__construct($orchestratorHandler);
    }

    /**
     * @param Request $request
     * @return Employee
     */
    public function invoke(Request $request): Employee
    {
        /** @var Employee $employee */
        $employee = $this->orchestratorHandler->handler('create-employee', $request);

        $request->merge(['image' => $employee->image()->value()]);
        $request->merge(['employeeId' => $employee->id()->value()]);

        /** @var User $user */
        $user = $this->orchestratorHandler->handler('create-user', $request);
        $employee->userId()->setValue($user->id()->value());

        return $employee;
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'create-employee-action';
    }
}
