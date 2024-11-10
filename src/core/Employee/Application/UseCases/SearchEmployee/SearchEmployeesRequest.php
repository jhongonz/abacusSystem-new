<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;

class SearchEmployeesRequest implements RequestService
{
    /**
     * @param array<string, mixed> $filters
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
