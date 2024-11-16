<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 22:56:24
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Illuminate\Http\Request;

class GetProfileOrchestrator extends ProfileOrchestrator
{
    public function __construct(ProfileManagementContract $profileManagement)
    {
        parent::__construct($profileManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $profileId = $request->integer('profileId');
        $profile = $this->profileManagement->searchProfileById($profileId);

        return ['profile' => $profile];
    }

    public function canOrchestrate(): string
    {
        return 'retrieve-profile';
    }
}
