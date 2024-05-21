<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:43:18
 */

namespace Core\Institution\Infrastructure\Management;

use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitution;
use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitutionRequest;
use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitution;
use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitutionRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionById;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutions;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionsRequest;
use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitution;
use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitutionRequest;
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
    private UpdateInstitution $updateInstitution;
    private CreateInstitution $createInstitution;
    private DeleteInstitution $deleteInstitution;

    public function __construct(
        InstitutionFactoryContract $institutionFactory,
        SearchInstitutionById $searchInstitutionById,
        SearchInstitutions $searchInstitutions,
        UpdateInstitution $updateInstitution,
        CreateInstitution $createInstitution,
        DeleteInstitution $deleteInstitution
    ) {
        $this->institutionFactory = $institutionFactory;
        $this->searchInstitutionById = $searchInstitutionById;
        $this->searchInstitutions = $searchInstitutions;
        $this->updateInstitution = $updateInstitution;
        $this->createInstitution = $createInstitution;
        $this->deleteInstitution = $deleteInstitution;
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

    /**
     * @throws Exception
     */
    public function updateInstitution(InstitutionId $id, array $data): Institution
    {
        $request = new UpdateInstitutionRequest($id, $data);

        return $this->updateInstitution->execute($request);
    }

    /**
     * @throws Exception
     */
    public function createInstitution(Institution $institution): Institution
    {
        $request = new CreateInstitutionRequest($institution);

        return $this->createInstitution->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteInstitution(InstitutionId $id): void
    {
        $request = new DeleteInstitutionRequest($id);

        $this->deleteInstitution->execute($request);
    }
}
