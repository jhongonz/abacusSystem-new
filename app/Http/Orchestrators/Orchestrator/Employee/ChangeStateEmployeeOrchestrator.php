<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:56:12
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Illuminate\Http\Request;

class ChangeStateEmployeeOrchestrator extends EmployeeOrchestrator
{
    public function __construct(EmployeeManagementContract $employeeManagement)
    {
        parent::__construct($employeeManagement);
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     * @throws EmployeeNotFoundException
     */
    public function make(Request $request): array
    {
        $employeeId = $request->integer('id');
        $employee = $this->employeeManagement->searchEmployeeById($employeeId);

        if (is_null($employee)) {
            throw new EmployeeNotFoundException(sprintf('Employee with id %s not found', $employeeId));
        }

        $employeeState = $employee->state();
        if ($employeeState->isNew() || $employeeState->isInactivated()) {
            $employeeState->activate();
        } elseif ($employeeState->isActivated()) {
            $employeeState->inactive();
        }

        $dataUpdate['state'] = $employeeState->value();

        $employee = $this->employeeManagement->updateEmployee($employeeId, $dataUpdate);
        return ['employee' => $employee];
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-employee';
    }
}
