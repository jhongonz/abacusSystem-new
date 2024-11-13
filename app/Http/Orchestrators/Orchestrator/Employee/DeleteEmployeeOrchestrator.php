<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:56:22
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Illuminate\Http\Request;

class DeleteEmployeeOrchestrator extends EmployeeOrchestrator
{
    public function __construct(EmployeeManagementContract $employeeManagement)
    {
        parent::__construct($employeeManagement);
    }
    /**
     * @inheritDoc
     */
    public function make(Request $request): bool
    {
        $this->employeeManagement->deleteEmployee($request->integer('employeeId'));
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canOrchestrate(): string
    {
        return 'delete-employee';
    }
}
