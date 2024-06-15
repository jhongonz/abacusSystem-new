<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:20:17
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Traits\UtilsDateTimeTrait;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;

class UpdateEmployeeOrchestrator extends EmployeeOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(
        EmployeeManagementContract $employeeManagement,
    ) {
        parent::__construct($employeeManagement);
    }
    /**
     * @param Request $request
     * @return Employee
     * @throws \Exception
     */
    public function make(Request $request): Employee
    {
        $dataUpdate = json_decode($request->input('dataUpdate'), true);
        $dataUpdate['updatedAt'] = $this->getCurrentTime();
        $dataUpdate['birthdate'] = $this->getDateTime($dataUpdate['birthdate']);

        return $this->employeeManagement->updateEmployee($request->input('employeeId'), $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-employee';
    }
}
