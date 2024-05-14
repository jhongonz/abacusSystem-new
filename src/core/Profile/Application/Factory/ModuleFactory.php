<?php

namespace Core\Profile\Application\Factory;

use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use DateTime;
use Exception;

class ModuleFactory implements ModuleFactoryContract
{
    /**
     * @throws Exception
     */
    public function buildModuleFromArray(array $data): Module
    {
        $data = $data[Module::TYPE];
        $module = $this->buildModule(
            $this->buildModuleId($data['id']),
            $this->buildModuleMenuKey($data['key']),
            $this->buildModuleName($data['name']),
            $this->buildModuleRoute($data['route']),
            $this->buildModuleIcon($data['icon']),
            $this->buildModuleState($data['state']),
        );

        if ($data['createdAt']) {
            $module->createdAt()->setValue(new DateTime($data['createdAt']['date']));
        }

        if ($data['updatedAt']) {
            $module->updatedAt()->setValue(new DateTime($data['updatedAt']['date']));
        }

        return $module;
    }

    public function buildModule(
        ModuleId $id,
        ModuleMenuKey $key,
        ModuleName $name,
        ModuleRoute $route,
        ModuleIcon $icon = new ModuleIcon,
        ModuleState $state = new ModuleState,
        ModuleCreatedAt $createdAt = new ModuleCreatedAt
    ): Module {

        return new Module(
            $id,
            $key,
            $name,
            $route,
            $icon,
            $state,
            $createdAt
        );
    }

    public function buildModuleId(?int $id = null): ModuleId
    {
        return new ModuleId($id);
    }

    public function buildModuleMenuKey(?string $key = null): ModuleMenuKey
    {
        return new ModuleMenuKey($key);
    }

    public function buildModuleName(string $name): ModuleName
    {
        return new ModuleName($name);
    }

    public function buildModuleRoute(?string $route = null): ModuleRoute
    {
        return new ModuleRoute($route);
    }

    public function buildModuleIcon(?string $icon = null): ModuleIcon
    {
        return new ModuleIcon($icon);
    }

    /**
     * @throws Exception
     */
    public function buildModuleState(?int $state = null): ModuleState
    {
        return new ModuleState($state);
    }

    public function buildModuleCreatedAt(DateTime $datetime = new DateTime): ModuleCreatedAt
    {
        return new ModuleCreatedAt($datetime);
    }

    public function buildModuleUpdatedAt(?DateTime $datetime = null): ModuleUpdatedAt
    {
        return new ModuleUpdatedAt($datetime);
    }

    public function buildModules(Module ...$modules): Modules
    {
        return new Modules(...$modules);
    }

    /**
     * @throws Exception
     */
    public function buildModulesFromArray(array $data): Modules
    {
        $data = $data[Modules::TYPE];

        $modules = new Modules;
        foreach ($data as $item) {
            $modules->addItem(
                $this->buildModuleFromArray($item)
            );
        }

        return $modules;
    }

    public function buildModuleSearch(?string $search = null): ModuleSearch
    {
        return new ModuleSearch($search);
    }
}
