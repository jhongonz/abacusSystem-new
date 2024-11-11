<?php

namespace Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\RequestService;

class SearchProfilesRequest implements RequestService
{
    /**
     * @param array<int|string, mixed> $filters
     */
    public function __construct(
        private readonly array $filters = []
    ) {
    }

    /**
     * @return array<int|string, mixed>
     */
    public function filters(): array
    {
        return $this->filters;
    }
}
