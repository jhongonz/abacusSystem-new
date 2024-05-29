<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 12:55:03
 */

namespace Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\RequestService;

class SearchInstitutionsRequest implements RequestService
{
    private array $filters;

    public function __construct(
        array $filters
    ) {
        $this->filters = $filters;
    }

    public function filters(): array
    {
        return $this->filters;
    }
}
