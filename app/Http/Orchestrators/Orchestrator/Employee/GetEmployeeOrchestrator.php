<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:58:20
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Illuminate\Http\Request;

class GetEmployeeOrchestrator extends EmployeeOrchestrator
{
    public function __construct(EmployeeManagementContract $employeeManagement)
    {
        parent::__construct($employeeManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        if ($request->filled('identification')) {
            $employee = $this->employeeManagement->searchEmployeeByIdentification($request->string('identification'));
        } else {
            $employeeId = $request->integer('employeeId');
            $employee = $this->employeeManagement->searchEmployeeById($employeeId);
        }

        return ['employee' => $employee];
    }

    public function canOrchestrate(): string
    {
        return 'retrieve-employee';
    }
}
