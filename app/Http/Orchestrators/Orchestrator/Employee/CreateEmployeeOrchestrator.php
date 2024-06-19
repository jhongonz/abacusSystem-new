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
        ImageManagerInterface $imageManager,
    ) {
        parent::__construct($employeeManagement);
        $this->setImageManager($imageManager);
    }

    /**
     * @param Request $request
     * @return Employee
     * @throws \Exception
     */
    public function make(Request $request): Employee
    {
        $birthdate = $request->date('birthdate', 'd/m/Y');
        $dataEmployee = [
            'id' => $request->input('employeeId'),
            'userId' => null,
            'institutionId' => $request->input('institutionId'),
            'identification' => $request->input('identifier'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'identification_type' => $request->input('typeDocument'),
            'observations' => $request->input('observations'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'birthdate' => $this->getDateTime($birthdate->format('Y-m-d'))->format(self::DATE_FORMAT),
            'state' => ValueObjectStatus::STATE_NEW,
            'image' => null
        ];

        if ($request->filled('token')) {
            $filename = $this->saveImage($request->input('token'));
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
