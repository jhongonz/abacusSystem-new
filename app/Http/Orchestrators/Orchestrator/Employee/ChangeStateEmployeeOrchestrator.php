<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:56:12
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Traits\UtilsDateTimeTrait;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;

class ChangeStateEmployeeOrchestrator extends EmployeeOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(EmployeeManagementContract $employeeManagement)
    {
        parent::__construct($employeeManagement);
    }

    /**
     * @param Request $request
     * @return Employee
     */
    public function make(Request $request): Employee
    {
        $employeeId = $request->input('id');
        $employee = $this->employeeManagement->searchEmployeeById($employeeId);

        $employeeState = $employee->state();
        if ($employeeState->isNew() || $employeeState->isInactivated()) {
            $employeeState->activate();
        } elseif ($employeeState->isActivated()) {
            $employeeState->inactive();
        }

        $dataUpdate['state'] = $employeeState->value();
        $dataUpdate['updatedAt'] = $this->getCurrentTime();

        return $this->employeeManagement->updateEmployee($employeeId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-employee';
    }
}
