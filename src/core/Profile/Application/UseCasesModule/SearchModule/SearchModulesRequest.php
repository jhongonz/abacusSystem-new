<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;

class SearchModulesRequest implements RequestService
{
    /**
     * @param array<string,mixed> $filters
     */
    public function __construct(
        private readonly array $filters = []
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return $this->filters;
    }
}
