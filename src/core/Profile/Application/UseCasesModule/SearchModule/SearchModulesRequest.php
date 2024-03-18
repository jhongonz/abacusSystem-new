<?php

namespace Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\RequestService;

class SearchModulesRequest implements RequestService
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