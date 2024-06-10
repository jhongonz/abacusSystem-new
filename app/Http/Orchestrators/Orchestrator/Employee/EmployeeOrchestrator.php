<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:42:34
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;

abstract class EmployeeOrchestrator implements Orchestrator
{
    protected EmployeeManagementContract $employeeManagement;

    public function __construct(
        EmployeeManagementContract $employeeManagement,
    ) {
        $this->employeeManagement = $employeeManagement;
    }
}
