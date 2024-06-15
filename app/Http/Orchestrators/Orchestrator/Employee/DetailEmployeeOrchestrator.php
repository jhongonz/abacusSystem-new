<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:14:35
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DetailEmployeeOrchestrator extends EmployeeOrchestrator
{
    private const IMAGE_PATH_FULL = '/images/full/';

    private UserManagementContract $userManagement;
    private ProfileManagementContract $profileManagement;

    public function __construct(
        EmployeeManagementContract $employeeManagement,
        UserManagementContract $userManagement,
        ProfileManagementContract $profileManagement
    ) {
        parent::__construct($employeeManagement);
        $this->userManagement = $userManagement;
        $this->profileManagement = $profileManagement;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function make(Request $request): array
    {
        $employee = null;
        $employeeId = $request->input('employeeId');

        if (! is_null($employeeId)) {
            $employee = $this->employeeManagement->searchEmployeeById($employeeId);
            $user = $this->userManagement->searchUserById($employee->userId()->value());

            $urlFile = url(self::IMAGE_PATH_FULL.$employee->image()->value()).'?v='.Str::random(10);
        }

        $profiles = $this->profileManagement->searchProfiles();
        $userId = (! is_null($employee)) ? $employee->userId()->value() : null;

        return [
            'userId' => $userId,
            'employeeId' => $employeeId,
            'employee' => $employee,
            'user' => $user ?? null,
            'profiles' => $profiles,
            'image' => $urlFile ?? null,
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
