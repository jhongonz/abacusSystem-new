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
use DateTime;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class CreateEmployeeOrchestrator extends EmployeeOrchestrator
{
    use MultimediaTrait;
    use UtilsDateTimeTrait;

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
        $dataEmployee = [
            'id' => $request->input('employeeId'),
            'identifier' => $request->input('identifier'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'typeDocument' => $request->input('typeDocument'),
            'observations' => $request->input('observations'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'birthdate' => DateTime::createFromFormat('d/m/Y', $request->input('birthdate')),
            'createAt' => $this->getCurrentTime(),
            'state' => ValueObjectStatus::STATE_NEW
        ];

        $token = $request->input('token');
        if (! is_null($token)) {
            $filename = $this->saveImage($token);
            $dataEmployee['image'] = $filename;
        }

        return $this->employeeManagement->createEmployee([Employee::TYPE => $dataEmployee]);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'create-employee';
    }
}
