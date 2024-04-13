<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;

class SearchEmployeesRequest implements RequestService
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function filters(): array
    {
        return $this->filters;
    }
}
