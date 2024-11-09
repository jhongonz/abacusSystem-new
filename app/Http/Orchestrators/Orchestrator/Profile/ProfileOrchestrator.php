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
        protected readonly ProfileManagementContract $profileManagement
    ) {
    }

    /**
     * @param Request $request
     * @return array<int<0, max>, mixed>
     */
    protected function getModulesAggregator(Request $request): array
    {
        $modulesAggregator = [];
        foreach ($request->input('modules') as $item) {
            $modulesAggregator[] = $item['id'];
        }

        return $modulesAggregator;
    }
}
