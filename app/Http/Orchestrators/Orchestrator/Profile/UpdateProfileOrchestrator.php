<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:24:46
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Illuminate\Http\Request;

class UpdateProfileOrchestrator extends ProfileOrchestrator
{
    public function __construct(
        ProfileManagementContract $profileManagement,
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $dataUpdate = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'modules' => $this->getModulesAggregator($request),
        ];

        $profile = $this->profileManagement->updateProfile($request->integer('profileId'), $dataUpdate);

        return ['profile' => $profile];
    }

    public function canOrchestrate(): string
    {
        return 'update-profile';
    }
}
