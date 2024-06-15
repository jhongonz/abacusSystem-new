<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;

interface ModuleManagementContract
{
    public function searchModuleById(?int $id): ?Module;

    public function searchModules(array $filters = []): Modules;

    public function createModule(array $dataModule): Module;

    public function updateModule(int $moduleId, array $data): Module;

    public function deleteModule(int $moduleId): void;
}
