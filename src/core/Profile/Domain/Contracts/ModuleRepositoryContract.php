<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;

interface ModuleRepositoryContract
{
    public function find(ModuleId $id): ?Module;

    public function getAll(array $filters = []): ?Modules;

    public function persistModule(Module $module): Module;

    public function deleteModule(ModuleId $id): void;
}
