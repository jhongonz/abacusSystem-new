<?php

namespace Core\Profile\Application\UseCasesModule\UpdateModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\ValueObjects\ModuleId;

class UpdateModuleRequest implements RequestService
{
    private ModuleId $moduleId;
    private array $data;
    
    public function __construct(
        ModuleId $moduleId,
        array $data
    ) {
        $this->moduleId = $moduleId;
        $this->data = $data;
    }
    
    public function moduleId(): ModuleId
    {
        return $this->moduleId;
    }
    
    public function data(): array
    {
        return $this->data;
    }
}