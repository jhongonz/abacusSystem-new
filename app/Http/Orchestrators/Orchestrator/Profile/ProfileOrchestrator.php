<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:06:59
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Illuminate\Http\Request;

abstract class ProfileOrchestrator implements Orchestrator
{
    public function __construct(
        protected readonly ProfileManagementContract $profileManagement,
    ) {
    }

    /**
     * @return array<int<0, max>, mixed>
     */
    protected function getModulesAggregator(Request $request): array
    {
        /** @var array<int|string, array<int|string, mixed>> $modules */
        $modules = $request->input('modules', []);

        $modulesAggregator = [];
        foreach ($modules as $item) {
            if (array_key_exists('id', $item)) {
                $modulesAggregator[] = $item['id'];
            }
        }

        return $modulesAggregator;
    }
}
