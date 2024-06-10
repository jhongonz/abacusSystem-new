<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:24:46
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Traits\UtilsDateTimeTrait;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class UpdateProfileOrchestrator extends ProfileOrchestrator
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
        $dataUpdate = json_decode($request->input('dataUpdate'), true);
        $dataUpdate['updatedAt'] = $this->getCurrentTime();

        return $this->profileManagement->updateProfile($profileId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-profile';
    }
}
