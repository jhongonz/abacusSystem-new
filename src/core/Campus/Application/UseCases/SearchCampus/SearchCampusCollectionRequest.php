<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:09:23
 */

namespace Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;

class SearchCampusCollectionRequest implements RequestService
{
    /**
     * @param CampusInstitutionId $institutionId
     * @param array<string, mixed> $filters
     */
    public function __construct(
        private readonly CampusInstitutionId $institutionId,
        private readonly array $filters = []
    ) {
    }

    public function institutionId(): CampusInstitutionId
    {
        return $this->institutionId;
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return $this->filters;
    }
}
