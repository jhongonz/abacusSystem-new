<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-07 23:50:52
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Traits\UtilsDateTimeTrait;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class ChangeStateProfileOrchestrator extends ProfileOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(
        ProfileManagementContract $profileManagement
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @param Request $request
     * @return Profile
     */
    public function make(Request $request): Profile
    {
        $profileId = $request->input('profileId');
        $profile = $this->profileManagement->searchProfileById($profileId);

        $state = $profile->state();
        if ($state->isNew() || $state->isInactivated()) {
            $state->activate();
        } elseif ($state->isActivated()) {
            $state->inactive();
        }

        $dataUpdate['state'] = $state->value();
        $dataUpdate['updateAt'] = $this->getCurrentTime();

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
