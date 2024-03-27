<?php

namespace Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Exception;

class UpdateEmployee extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|Employee|Employees
    {
        $this->validateRequest($request, UpdateEmployeeRequest::class);

        $employee = $this->employeeRepository->find($request->employeeId());
        foreach ($request->data() as $field => $value) {
            $methodName = 'change'.ucfirst($field);

            if (is_callable([$this, $methodName])) {
                $employee = $this->{$methodName}($employee, $value);
            }
        }

        $employee->refreshSearch();
        return $this->employeeRepository->persistEmployee($employee);
    }

    /**
     * @throws Exception
     */
    private function changeState(Employee $employee, int $state): Employee
    {
        $employee->state()->setValue($state);
        return $employee;
    }
}
