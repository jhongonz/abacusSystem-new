<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:20:17
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Traits\MultimediaTrait;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use DateTime;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class UpdateEmployeeOrchestrator extends EmployeeOrchestrator
{
    use MultimediaTrait;

    public function __construct(
        EmployeeManagementContract $employeeManagement,
        ImageManagerInterface $imageManager,
    ) {
        parent::__construct($employeeManagement);
        $this->setImageManager($imageManager);
    }
    /**
     * @param Request $request
     * @return Employee
     */
    public function make(Request $request): Employee
    {
        $birthdate = $request->input('birthdate');

        $dataUpdate = [
            'identifier' => $request->input('identifier'),
            'typeDocument' => $request->input('typeDocument'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
            'birthdate' => ($birthdate) ? DateTime::createFromFormat('d/m/Y', $birthdate) : $birthdate,
        ];

        $imageToken = $request->input('token');
        if (! is_null($imageToken)) {
            $filename = $this->saveImage($imageToken);
            $dataUpdate['image'] = $filename;
        }

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
