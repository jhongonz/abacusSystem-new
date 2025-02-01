<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:15:58
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;

class CreateProfileOrchestrator extends ProfileOrchestrator
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
        $dataProfile = [
            'id' => null,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'modulesAggregator' => $this->getModulesAggregator($request),
            'state' => ValueObjectStatus::STATE_NEW,
        ];

        $profile = $this->profileManagement->createProfile([Profile::TYPE => $dataProfile]);

        return ['profile' => $profile];
    }

    public function canOrchestrate(): string
    {
        return 'create-profile';
    }
}
