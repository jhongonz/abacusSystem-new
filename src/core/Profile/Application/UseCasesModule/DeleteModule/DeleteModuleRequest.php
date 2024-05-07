<?php

namespace Core\Profile\Application\UseCasesModule\DeleteModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\ValueObjects\ModuleId;

class DeleteModuleRequest implements RequestService
{
    private ModuleId $id;

    public function __construct(ModuleId $id)
    {
        $this->id = $id;
    }

    public function id(): ModuleId
    {
        return $this->id;
    }
}
