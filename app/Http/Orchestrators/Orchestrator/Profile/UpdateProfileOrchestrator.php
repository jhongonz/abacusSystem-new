<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:24:46
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class UpdateProfileOrchestrator extends ProfileOrchestrator
{
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
        $dataUpdate = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'modules' => $this->getModulesAggregator($request)
        ];

        return $this->profileManagement->updateProfile($request->integer('profileId'), $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-profile';
    }
}
