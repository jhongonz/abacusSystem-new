<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\ValueObjects\ModuleId;

class SearchModuleByIdRequest implements RequestService
{
    private ModuleId $id;

    public function __construct(ModuleId $id)
    {
        $this->id = $id;
    }

    public function moduleId(): ModuleId
    {
        return $this->id;
    }
}
