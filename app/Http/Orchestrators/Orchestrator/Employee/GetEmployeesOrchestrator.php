<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:33:33
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;

class GetEmployeesOrchestrator extends EmployeeOrchestrator
{
    public function __construct(
        EmployeeManagementContract $employeeManagement,
        private readonly EmployeeDataTransformerContract $employeeDataTransformer,
    ) {
        parent::__construct($employeeManagement);
    }

    /**
     * @param Request $request
     * @return array<int|string, mixed>
     */
    public function make(Request $request): array
    {
        $employees = $this->employeeManagement->searchEmployees(
            (array) $request->input('filters')
        );

        $dataEmployees = [];
        if ($employees->count()) {
            /** @var Employee $item */
            foreach ($employees as $item) {
                $dataEmployees[] = $this->employeeDataTransformer->write($item)->readToShare();
            }
        }

        return $dataEmployees;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-employees';
    }
}
