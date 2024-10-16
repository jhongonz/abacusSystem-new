<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;

class SearchModulesRequest implements RequestService
{
    public function __construct(
        private readonly array $filters = []
    ) {
    }

    public function filters(): array
    {
        return $this->filters;
    }
}
