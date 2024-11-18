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

    public function invoke(Request $request): Employee
    {
        /** @var array{employee: Employee} $dataEmployee */
        $dataEmployee = $this->orchestratorHandler->handler('create-employee', $request);
        $employee = $dataEmployee['employee'];

        $request->merge(['image' => $employee->image()->value()]);
        $request->merge(['employeeId' => $employee->id()->value()]);

        /** @var array{user: User} $dataUser */
        $dataUser = $this->orchestratorHandler->handler('create-user', $request);
        $user = $dataUser['user'];

        if (!is_null($user->id()->value())) {
            $employee->userId()->setValue($user->id()->value());
        }

        return $employee;
    }

    public function canExecute(): string
    {
        return 'create-employee-action';
    }
}
