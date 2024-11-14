<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-07 23:50:52
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Illuminate\Http\Request;

class ChangeStateProfileOrchestrator extends ProfileOrchestrator
{
    public function __construct(
        ProfileManagementContract $profileManagement
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @param Request $request
     * @return Profile
     * @throws ProfileNotFoundException
     */
    public function make(Request $request): Profile
    {
        $profileId = $request->integer('profileId');
        $profile = $this->profileManagement->searchProfileById($profileId);

        if (is_null($profile)) {
            throw new ProfileNotFoundException(sprintf('Profile with id %s not found', $profileId));
        }

        $state = $profile->state();
        if ($state->isNew() || $state->isInactivated()) {
            $state->activate();
        } elseif ($state->isActivated()) {
            $state->inactive();
        }

        $dataUpdate['state'] = $state->value();
        return $this->profileManagement->updateProfile($profileId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-profile';
    }
}
