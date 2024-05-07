<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;

interface ModuleDataTransformerContract
{
    public function write(Module $module): ModuleDataTransformerContract;

    public function read(): array;

    public function readToShare(): array;
}
