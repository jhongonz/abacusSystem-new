<?php

namespace Core\Profile\Application\UseCasesModule\CreateModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\Module;

class CreateModuleRequest implements RequestService
{
    public function __construct(
        private readonly Module $module
    ) {
    }

    public function module(): Module
    {
        return $this->module;
    }
}
