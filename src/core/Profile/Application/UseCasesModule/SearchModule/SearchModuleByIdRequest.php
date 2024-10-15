<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;
use Core\Profile\Domain\ValueObjects\ModuleId;

class SearchModuleByIdRequest implements RequestService
{
    public function __construct(
        private readonly ModuleId $id
    ) {
    }

    public function moduleId(): ModuleId
    {
        return $this->id;
    }
}
