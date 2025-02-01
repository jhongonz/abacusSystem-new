<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Module;

interface ModuleDataTransformerContract
{
    public function write(Module $module): ModuleDataTransformerContract;

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function read(): array;

    /**
     * @return array<string, mixed>
     */
    public function readToShare(): array;
}
