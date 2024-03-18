<?php

namespace Core\Profile\Application\UseCasesModule\CreateModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\Module;

class CreateModuleRequest implements RequestService
{
    private Module $module;
    
    public function __construct(Module $module)
    {
        $this->module = $module;
    }
    
    public function module(): Module
    {
        return $this->module;
    }
}