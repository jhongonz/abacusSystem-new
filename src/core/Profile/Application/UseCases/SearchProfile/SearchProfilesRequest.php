<?php

namespace Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\RequestService;

class SearchProfilesRequest implements RequestService
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
