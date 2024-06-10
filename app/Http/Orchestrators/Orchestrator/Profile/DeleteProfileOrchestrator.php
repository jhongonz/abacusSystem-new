<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-07 23:59:10
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Illuminate\Http\Request;

class DeleteProfileOrchestrator extends ProfileOrchestrator
{
    public function __construct(
        ProfileManagementContract $profileManagement
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function make(Request $request): bool
    {
        $this->profileManagement->deleteProfile($request->input('profileId'));

        return true;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'delete-profile';
    }
}
