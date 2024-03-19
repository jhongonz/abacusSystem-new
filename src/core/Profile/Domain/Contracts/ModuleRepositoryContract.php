<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;

interface ModuleRepositoryContract
{
    public function find(ModuleId $id): null|Module;

    public function getAll(array $filters = []): null|Modules;

    public function persistModule(Module $module): Module;

    public function deleteModule(ModuleId $id): void;
}
