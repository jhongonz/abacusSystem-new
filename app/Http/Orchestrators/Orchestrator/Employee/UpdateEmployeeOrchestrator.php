<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:20:17
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Traits\UtilsDateTimeTrait;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
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
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function make(Request $request): array
    {
        /** @var array<string, mixed> $dataUpdate */
        $dataUpdate = json_decode($request->string('dataUpdate'), true);

        if (array_key_exists('birthdate', $dataUpdate)) {
            /** @var string $birthdate */
            $birthdate = $dataUpdate['birthdate'];

            $dataUpdate['birthdate'] = $this->getDateTime($birthdate);
        }

        $employee = $this->employeeManagement->updateEmployee($request->integer('employeeId'), $dataUpdate);

        return ['employee' => $employee];
    }

    public function canOrchestrate(): string
    {
        return 'update-employee';
    }
}
