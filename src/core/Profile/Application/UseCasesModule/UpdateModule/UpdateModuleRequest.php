<?php

namespace Core\Profile\Application\UseCasesModule\UpdateModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\ValueObjects\ModuleId;

class UpdateModuleRequest implements RequestService
{
    /**
     * @param ModuleId $moduleId
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly ModuleId $moduleId,
        private readonly array $data
    ) {
    }

    public function moduleId(): ModuleId
    {
        return $this->moduleId;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }
}
