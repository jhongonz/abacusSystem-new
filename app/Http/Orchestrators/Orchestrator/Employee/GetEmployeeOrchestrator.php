<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:58:20
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;

class GetEmployeeOrchestrator extends EmployeeOrchestrator
{
    public function __construct(EmployeeManagementContract $employeeManagement)
    {
        parent::__construct($employeeManagement);
    }

    /**
     * @param Request $request
     * @return Employee|null
     */
    public function make(Request $request): ?Employee
    {
        $employeeId = $request->input('employeeId');

        return $this->employeeManagement->searchEmployeeById($employeeId);
    }

    /**
     * @inheritDoc
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-employee';
    }
}
