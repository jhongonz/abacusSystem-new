<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:08:18
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;

class GetProfilesOrchestrator extends ProfileOrchestrator
{
    public function __construct(
        ProfileManagementContract $profileManagement,
        private readonly ProfileDataTransformerContract $profileDataTransformer,
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function make(Request $request): array
    {
        /** @var array<string, mixed> $filters */
        $filters = $request->input('filters');

        $profiles = $this->profileManagement->searchProfiles($filters);

        $dataProfiles = [];
        if ($profiles->count()) {
            /** @var Profile $item */
            foreach ($profiles as $item) {
                $dataProfiles[] = $this->profileDataTransformer->write($item)->readToShare();
            }
        }

        return $dataProfiles;
    }

    public function canOrchestrate(): string
    {
        return 'retrieve-profiles';
    }
}
