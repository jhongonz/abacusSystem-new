<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;

class SearchEmployeesRequest implements RequestService
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
