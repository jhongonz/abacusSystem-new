<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:03:46
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;

class DetailProfileOrchestrator extends ProfileOrchestrator
{
    public function __construct(
        ProfileManagementContract $profileManagement,
        private readonly ModuleManagementContract $moduleManagement,
        private readonly Config $config,
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $profileId = $request->integer('profileId') ?: null;

        $profile = null;
        if (!is_null($profileId)) {
            $profile = $this->profileManagement->searchProfileById($profileId);
        }

        $modules = $this->moduleManagement->searchModules();
        $privileges = $this->retrievePrivilegesProfile($modules, $profile);

        return [
            'profileId' => $profileId,
            'profile' => $profile,
            'modules' => $modules,
            'privileges' => $privileges,
        ];
    }

    public function canOrchestrate(): string
    {
        return 'detail-profile';
    }

    /**
     * @return array<int|string, mixed>
     */
    private function retrievePrivilegesProfile(Modules $modules, ?Profile $profile): array
    {
        $modulesToProfile = (!is_null($profile)) ? $profile->modulesAggregator() : [];

        /** @var array<string, mixed> $parents */
        $parents = $this->config->get('menu.options');

        $privileges = [];

        /** @var array{key: string} $item */
        foreach ($parents as $item) {
            /** @var string $key */
            $key = $item['key'];

            $modulesParent = $modules->moduleElementsOfKey($key);

            if (!empty($modulesParent)) {
                $privileges[$key]['menu'] = $item;
                $privileges[$key]['children'] = [];
            }

            foreach ($modulesParent as $module) {
                if ($module->state()->isActivated()) {
                    $privileges[$key]['children'][] = [
                        'module' => $module,
                        'selected' => in_array($module->id()->value(), $modulesToProfile),
                    ];
                }
            }
        }

        return $privileges;
    }
}
