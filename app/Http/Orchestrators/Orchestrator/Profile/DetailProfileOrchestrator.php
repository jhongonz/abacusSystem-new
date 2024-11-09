<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:03:46
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Module;
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
     * @param Request $request
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $profileId = $request->input('profileId');
        $profile = null;

        if (isset($profileId)) {

            /** @var Profile $profile */
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

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'detail-profile';
    }

    /**
     * @param Modules $modules
     * @param Profile|null $profile
     * @return array<string, mixed>
     */
    private function retrievePrivilegesProfile(Modules $modules, ?Profile $profile): array
    {
        $modulesToProfile = (isset($profile)) ? $profile->modulesAggregator() : [];
        $parents = $this->config->get('menu.options');
        $privileges = [];

        foreach ($parents as $index => $item) {
            $modulesParent = $modules->moduleElementsOfKey($index);

            if (count($modulesParent) > 0) {
                $privileges[$index]['menu'] = $item;
                $privileges[$index]['children'] = [];
            }

            /** @var Module $module */
            foreach ($modulesParent as $module) {
                if ($module->state()->isActivated()) {
                    $privileges[$index]['children'][] = [
                        'module' => $module,
                        'selected' => in_array($module->id()->value(), $modulesToProfile),
                    ];
                }
            }
        }

        return $privileges;
    }
}
