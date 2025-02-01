<?php

namespace Core\Profile\Application\UseCasesModule\DeleteModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\ValueObjects\ModuleId;

class DeleteModuleRequest implements RequestService
{
    public function __construct(
        private readonly ModuleId $id,
    ) {
    }

    public function id(): ModuleId
    {
        return $this->id;
    }
}
