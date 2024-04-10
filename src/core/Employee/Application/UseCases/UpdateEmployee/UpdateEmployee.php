<?php

namespace Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use DateTime;
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

    public function changeIdentifier(Employee $employee, string $identifier): Employee
    {
        $employee->identification()->setValue($identifier);
        return $employee;
    }

    public function changeTypeDocument(Employee $employee, string $type): Employee
    {
        $employee->identificationType()->setValue($type);
        return $employee;
    }

    public function changeName(Employee $employee, string $name): Employee
    {
        $employee->name()->setValue($name);
        return $employee;
    }

    public function changeLastname(Employee $employee, string $lastname): Employee
    {
        $employee->lastname()->setValue($lastname);
        return $employee;
    }

    public function changeEmail(Employee $employee, string $email): Employee
    {
        $employee->email()->setValue($email);
        return $employee;
    }

    public function changePhone(Employee $employee, string $phone): Employee
    {
        $employee->phone()->setValue($phone);
        return $employee;
    }

    public function changeAddress(Employee $employee, string $address): Employee
    {
        $employee->address()->setValue($address);
        return $employee;
    }

    public function changeObservations(Employee $employee, string $observations): Employee
    {
        $employee->observations()->setValue($observations);
        return $employee;
    }

    public function changeBirthdate(Employee $employee, DateTime $birthdate): Employee
    {
        $employee->birthdate()->setValue($birthdate);
        return $employee;
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
