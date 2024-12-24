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
use Core\Profile\Domain\ValueObjects\ModulePosition;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use Core\SharedContext\Model\ValueObjectStatus;

class ModuleFactory implements ModuleFactoryContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function buildModuleFromArray(array $data): Module
    {
        /** @var array{
         *     id: int,
         *     key: string,
         *     name: string,
         *     route: string,
         *     icon: string|null,
         *     state: int|null,
         *     position: int|null,
         *     createdAt: string|null,
         *     updatedAt: string|null
         * } $dataModule
         */
        $dataModule = $data[Module::TYPE];

        $module = $this->buildModule(
            $this->buildModuleId($dataModule['id']),
            $this->buildModuleMenuKey($dataModule['key']),
            $this->buildModuleName($dataModule['name']),
            $this->buildModuleRoute($dataModule['route']),
            $this->buildModuleIcon($dataModule['icon']),
        );

        if (isset($dataModule['state'])) {
            $module->state()->setValue($dataModule['state']);
        }

        if (isset($dataModule['position'])) {
            $module->position()->setValue($dataModule['position']);
        }

        if (isset($dataModule['createdAt'])) {
            $module->createdAt()->setValue($this->getDateTime($dataModule['createdAt']));
        }

        if (isset($dataModule['updatedAt'])) {
            $module->updatedAt()->setValue($this->getDateTime($dataModule['updatedAt']));
        }

        return $module;
    }

    public function buildModule(
        ModuleId $id,
        ModuleMenuKey $key,
        ModuleName $name,
        ModuleRoute $route,
        ModuleIcon $icon = new ModuleIcon(),
        ModuleState $state = new ModuleState(),
        ModuleCreatedAt $createdAt = new ModuleCreatedAt(),
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

    public function buildModuleMenuKey(string $key = ''): ModuleMenuKey
    {
        return new ModuleMenuKey($key);
    }

    public function buildModuleName(string $name): ModuleName
    {
        return new ModuleName($name);
    }

    public function buildModuleRoute(string $route = ''): ModuleRoute
    {
        return new ModuleRoute($route);
    }

    public function buildModuleIcon(?string $icon = null): ModuleIcon
    {
        return new ModuleIcon($icon);
    }

    /**
     * @throws \Exception
     */
    public function buildModuleState(int $state = ValueObjectStatus::STATE_NEW): ModuleState
    {
        return new ModuleState($state);
    }

    public function buildModuleCreatedAt(\DateTime $datetime = new \DateTime()): ModuleCreatedAt
    {
        return new ModuleCreatedAt($datetime);
    }

    public function buildModuleUpdatedAt(?\DateTime $datetime = null): ModuleUpdatedAt
    {
        return new ModuleUpdatedAt($datetime);
    }

    public function buildModules(Module ...$modules): Modules
    {
        return new Modules($modules);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function buildModulesFromArray(array $data): Modules
    {
        /** @var array<string|int, array<string, mixed>> $dataModules */
        $dataModules = $data[Modules::TYPE];

        $modules = new Modules();
        foreach ($dataModules as $item) {
            $module = $this->buildModuleFromArray($item);
            $modules->addItem($module);
        }

        return $modules;
    }

    public function buildModuleSearch(?string $search = null): ModuleSearch
    {
        return new ModuleSearch($search);
    }

    public function buildModulePosition(int $position = 1): ModulePosition
    {
        return new ModulePosition($position);
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $dateTime): \DateTime
    {
        return new \DateTime($dateTime);
    }
}
