<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:14:35
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

class DetailEmployeeOrchestrator extends EmployeeOrchestrator
{
    private const IMAGE_PATH_FULL = '/images/full/';

    public function __construct(
        EmployeeManagementContract $employeeManagement,
        private readonly UserManagementContract $userManagement,
        private readonly ProfileManagementContract $profileManagement,
        private readonly InstitutionManagementContract $institutionManagement,
        private readonly UrlGenerator $urlGenerator
    ) {
        parent::__construct($employeeManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $employeeId = $request->integer('employeeId') ?: null;

        $employee = null;
        if (!is_null($employeeId)) {
            $employee = $this->employeeManagement->searchEmployeeById($employeeId);
        }

        if ($employee instanceof Employee) {
            $userId = $employee->userId()->value();

            if (!is_null($userId)) {
                $user = $this->userManagement->searchUserById($userId);
            }

            $path = sprintf('%s%s?v=%s',
                self::IMAGE_PATH_FULL,
                $employee->image()->value(),
                Str::random()
            );
            $urlFile = $this->urlGenerator->asset($path);
        }

        $institutions = $this->institutionManagement->searchInstitutions();
        $profiles = $this->profileManagement->searchProfiles();

        return [
            'userId' => $userId ?? null,
            'employeeId' => $employeeId,
            'employee' => $employee,
            'user' => $user ?? null,
            'profiles' => $profiles,
            'institutions' => $institutions,
            'image' => $urlFile ?? null,
        ];
    }

    public function canOrchestrate(): string
    {
        return 'detail-employee';
    }
}
