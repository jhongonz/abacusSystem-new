<?php

namespace Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;

class UpdateEmployee extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): Employee
    {
        $this->validateRequest($request, UpdateEmployeeRequest::class);

        /** @var UpdateEmployeeRequest $request */
        $employee = $this->employeeRepository->find($request->employeeId());

        foreach ($request->data() as $field => $value) {
            $methodName = $this->getFunctionName($field);

            if (is_callable([$this, $methodName])) {
                $employee = $this->{$methodName}($employee, $value);
            }
        }

        $employee->refreshSearch();

        return $this->employeeRepository->persistEmployee($employee);
    }

    protected function getFunctionName(string $field): string
    {
        return sprintf('change%s', ucfirst($field));
    }

    private function changeIdentifier(Employee $employee, string $identifier): Employee
    {
        $employee->identification()->setValue($identifier);

        return $employee;
    }

    private function changeTypeDocument(Employee $employee, string $type): Employee
    {
        $employee->identificationType()->setValue($type);

        return $employee;
    }

    private function changeName(Employee $employee, string $name): Employee
    {
        $employee->name()->setValue($name);

        return $employee;
    }

    private function changeLastname(Employee $employee, string $lastname): Employee
    {
        $employee->lastname()->setValue($lastname);

        return $employee;
    }

    private function changeEmail(Employee $employee, string $email): Employee
    {
        $employee->email()->setValue($email);

        return $employee;
    }

    private function changePhone(Employee $employee, string $phone): Employee
    {
        $employee->phone()->setValue($phone);

        return $employee;
    }

    private function changeAddress(Employee $employee, ?string $address): Employee
    {
        $employee->address()->setValue($address);

        return $employee;
    }

    private function changeObservations(Employee $employee, ?string $observations): Employee
    {
        $employee->observations()->setValue($observations);

        return $employee;
    }

    private function changeBirthdate(Employee $employee, ?\DateTime $birthdate): Employee
    {
        $employee->birthdate()->setValue($birthdate);

        return $employee;
    }

    /**
     * @throws \Exception
     */
    private function changeState(Employee $employee, int $state): Employee
    {
        $employee->state()->setValue($state);

        return $employee;
    }

    private function changeImage(Employee $employee, string $image): Employee
    {
        $employee->image()->setValue($image);

        return $employee;
    }

    private function changeUserId(Employee $employee, int $userId): Employee
    {
        $employee->userId()->setValue($userId);

        return $employee;
    }

    private function changeInstitutionId(Employee $employee, int $id): Employee
    {
        $employee->institutionId()->setValue($id);

        return $employee;
    }
}
