<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;

interface ModuleManagementContract
{
    public function searchModuleById(?int $id): ?Module;

    /**
     * @param array<string, mixed> $filters
     */
    public function searchModules(array $filters = []): Modules;

    /**
     * @param array<string, mixed> $dataModule
     */
    public function createModule(array $dataModule): Module;

    /**
     * @param array<string, mixed> $data
     */
    public function updateModule(int $moduleId, array $data): Module;

    public function deleteModule(int $moduleId): void;
}
