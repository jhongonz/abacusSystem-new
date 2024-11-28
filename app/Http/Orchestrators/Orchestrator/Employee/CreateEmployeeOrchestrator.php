<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:35:31
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Traits\MultimediaTrait;
use App\Traits\UtilsDateTimeTrait;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class CreateEmployeeOrchestrator extends EmployeeOrchestrator
{
    use MultimediaTrait;
    use UtilsDateTimeTrait;

    public function __construct(
        EmployeeManagementContract $employeeManagement,
        private readonly ImageManagerInterface $imageManager,
    ) {
        parent::__construct($employeeManagement);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function make(Request $request): array
    {
        $dataEmployee = [
            'id' => $request->integer('employeeId'),
            'userId' => null,
            'institutionId' => $request->integer('institutionId'),
            'identification' => $request->input('identifier'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'identification_type' => $request->input('typeDocument'),
            'observations' => $request->input('observations'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'state' => ValueObjectStatus::STATE_NEW,
            'image' => null,
        ];

        $birthdate = $request->date('birthdate', 'd/m/Y');
        if (!is_null($birthdate)) {
            $dataEmployee['birthdate'] = $this->getDateTime($birthdate->format('Y-m-d'))->format('Y-m-d H:i:s');
        }

        if ($request->filled('token')) {
            $filename = $this->saveImage($request->string('token'));
            $dataEmployee['image'] = $filename;
        }

        $employee = $this->employeeManagement->createEmployee([Employee::TYPE => $dataEmployee]);

        return ['employee' => $employee];
    }

    public function canOrchestrate(): string
    {
        return 'create-employee';
    }
}
