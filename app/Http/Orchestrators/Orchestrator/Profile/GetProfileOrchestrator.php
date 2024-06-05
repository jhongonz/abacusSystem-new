<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 22:56:24
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class GetProfileOrchestrator extends ProfileOrchestrator
{
    public function __construct(ProfileManagementContract $profileManagement)
    {
        parent::__construct($profileManagement);
    }

    /**
     * @param Request $request
     * @return Profile|null
     */
    public function make(Request $request): ?Profile
    {
        $profileId = $request->input('profileId');

        return $this->profileManagement->searchProfileById($profileId);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-profile';
    }
}
