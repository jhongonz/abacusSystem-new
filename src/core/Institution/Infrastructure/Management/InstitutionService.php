<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:43:18
 */

namespace Core\Institution\Infrastructure\Management;

use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionById;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutions;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionsRequest;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Exception;

class InstitutionService implements InstitutionManagementContract
{
    private InstitutionFactoryContract $institutionFactory;
    private SearchInstitutionById $searchInstitutionById;
    private SearchInstitutions $searchInstitutions;
    public function __construct(
        InstitutionFactoryContract $institutionFactory,
        SearchInstitutionById $searchInstitutionById,
        SearchInstitutions $searchInstitutions,
    ) {
        $this->institutionFactory = $institutionFactory;
        $this->searchInstitutionById = $searchInstitutionById;
        $this->searchInstitutions = $searchInstitutions;
    }

    /**
     * @throws Exception
     */
    public function searchInstitutionById(InstitutionId $id): ?Institution
    {
        $request = new SearchInstitutionByIdRequest($id);

        return $this->searchInstitutionById->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchInstitutions(array $filters = []): Institutions
    {
        $request = new SearchInstitutionsRequest($filters);
        $institutions = $this->searchInstitutions->execute($request);

        foreach ($institutions->aggregator() as $item) {
            $institution = $this->searchInstitutionById($this->institutionFactory->buildInstitutionId($item));
            $institutions->addItem($institution);
        }

        return $institutions;
    }

    public function updateInstitution(InstitutionId $id, array $data): void
    {
        // TODO: Implement updateInstitution() method.
    }

    public function createInstitution(Institution $institution): void
    {
        // TODO: Implement createInstitution() method.
    }

    public function deleteInstitution(InstitutionId $id): void
    {
        // TODO: Implement deleteInstitution() method.
    }
}
