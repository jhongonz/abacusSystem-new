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
use Exception;

class InstitutionService implements InstitutionManagementContract
{
    public function __construct(
        private readonly InstitutionFactoryContract $institutionFactory,
        private readonly SearchInstitutionById $searchInstitutionById,
        private readonly SearchInstitutions $searchInstitutions,
        private readonly UpdateInstitution $updateInstitution,
        private readonly CreateInstitution $createInstitution,
        private readonly DeleteInstitution $deleteInstitution
    ) {
    }

    /**
     * @throws Exception
     */
    public function searchInstitutionById(?int $id): ?Institution
    {
        $request = new SearchInstitutionByIdRequest(
            $this->institutionFactory->buildInstitutionId($id)
        );

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
            $institution = $this->searchInstitutionById($item);
            $institutions->addItem($institution);
        }

        return $institutions;
    }

    /**
     * @throws Exception
     */
    public function updateInstitution(int $id, array $data): Institution
    {
        $institutionId = $this->institutionFactory->buildInstitutionId($id);
        $request = new UpdateInstitutionRequest($institutionId, $data);

        return $this->updateInstitution->execute($request);
    }

    /**
     * @param array $data
     * @return Institution
     * @throws Exception
     */
    public function createInstitution(array $data): Institution
    {
        $institution = $this->institutionFactory->buildInstitutionFromArray($data);
        $request = new CreateInstitutionRequest($institution);

        return $this->createInstitution->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteInstitution(int $id): void
    {
        $request = new DeleteInstitutionRequest(
            $this->institutionFactory->buildInstitutionId($id)
        );

        $this->deleteInstitution->execute($request);
    }
}
