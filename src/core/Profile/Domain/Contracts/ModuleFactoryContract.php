<?php

namespace Core\Profile\Domain\Contracts;

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

interface ModuleFactoryContract
{
    public function buildModuleFromArray(array $data): Module;

    public function buildModule(
        ModuleId $id,
        ModuleMenuKey $key,
        ModuleName $name,
        ModuleRoute $route,
        ModuleIcon $icon = new ModuleIcon(),
        ModuleState $state = new ModuleState(),
        ModuleCreatedAt $createdAt = new ModuleCreatedAt()
    ): Module;

    public function buildModuleId(?int $id = null): ModuleId;

    public function buildModuleMenuKey(?string $key = null): ModuleMenuKey;

    public function buildModuleName(string $name): ModuleName;

    public function buildModuleRoute(?string $route = null): ModuleRoute;

    public function buildModuleIcon(?string $icon = null): ModuleIcon;

    public function buildModuleState(?int $state = null): ModuleState;

    public function buildModuleCreatedAt(DateTime $datetime): ModuleCreatedAt;

    public function buildModuleUpdatedAt(?DateTime $datetime = null): ModuleUpdatedAt;

    public function buildModuleSearch(?string $search = null): ModuleSearch;

    public function buildModules(Module ...$modules): Modules;

    public function buildModulesFromArray(array $data): Modules;
}
