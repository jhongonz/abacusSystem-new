<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:14:35
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DetailEmployeeOrchestrator extends EmployeeOrchestrator
{
    private const IMAGE_PATH_FULL = '/images/full/';

    public function __construct(
        EmployeeManagementContract $employeeManagement,
        private readonly UserManagementContract $userManagement,
        private readonly ProfileManagementContract $profileManagement,
        private readonly InstitutionManagementContract $institutionManagement
    ) {
        parent::__construct($employeeManagement);
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     * @throws EmployeeNotFoundException
     */
    public function make(Request $request): array
    {
        $employeeId = $request->integer('employeeId');
        $employee = $this->employeeManagement->searchEmployeeById($employeeId);

        if (is_null($employee)) {
            throw new EmployeeNotFoundException(sprintf('Employee with id %s not found', $employeeId));
        }

        $userId = $employee->userId()->value();
        $user = $this->userManagement->searchUserById($userId);
        
        $urlFile = url(self::IMAGE_PATH_FULL.$employee->image()->value()).'?v='.Str::random(10);
        $institutions = $this->institutionManagement->searchInstitutions();
        $profiles = $this->profileManagement->searchProfiles();

        return [
            'userId' => $userId,
            'employeeId' => $employeeId,
            'employee' => $employee,
            'user' => $user,
            'profiles' => $profiles,
            'institutions' => $institutions,
            'image' => $urlFile,
        ];
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'detail-employee';
    }
}
