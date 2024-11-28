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
        ProfileManagementContract $profileManagement,
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @return array<null>
     */
    public function make(Request $request): array
    {
        $this->profileManagement->deleteProfile($request->integer('profileId'));

        return [];
    }

    public function canOrchestrate(): string
    {
        return 'delete-profile';
    }
}
