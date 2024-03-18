<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;

interface ModuleManagementContract
{
    public function searchModuleById(ModuleId $id): Module;

    public function searchModules(array $filters): Modules;

    public function createModule(Module $module): Module;

    public function updateModule(ModuleId $moduleId, array $data): void;

    public function deleteModule(ModuleId $moduleId): void;
}