<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:08:18
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profiles;
use Illuminate\Http\Request;

class GetProfilesOrchestrator extends ProfileOrchestrator
{
    public function __construct(ProfileManagementContract $profileManagement)
    {
        parent::__construct($profileManagement);
    }

    /**
     * @param Request $request
     * @return Profiles
     */
    public function make(Request $request): Profiles
    {
        $filters = $request->input('filters', []);

        return $this->profileManagement->searchProfiles($filters);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-profiles';
    }
}
