<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 13:12:20
 */

namespace Core\Campus\Application\Factory;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\ValueObjects\CampusAddress;
use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use Core\Campus\Domain\ValueObjects\CampusEmail;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Domain\ValueObjects\CampusName;
use Core\Campus\Domain\ValueObjects\CampusObservations;
use Core\Campus\Domain\ValueObjects\CampusPhone;
use Core\Campus\Domain\ValueObjects\CampusSearch;
use Core\Campus\Domain\ValueObjects\CampusState;
use Core\Campus\Domain\ValueObjects\CampusUpdatedAt;

class CampusFactory implements CampusFactoryContract
{
    public function buildCampus(
        CampusId $id,
        CampusInstitutionId $institutionId,
        CampusName $name,
    ): Campus {
        return new Campus(
            $id,
            $institutionId,
            $name
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function buildCampusFromArray(array $data): Campus
    {
        /** @var array{
         *     id: int|null,
         *     institutionId: int,
         *     name: string,
         *     address: string|null,
         *     phone: string,
         *     email: string,
         *     observations: string|null,
         *     state: int,
         *     createdAt: string|null,
         *     updatedAt: string|null
         *     } $dataCampus */
        $dataCampus = $data[Campus::TYPE];

        $campus = $this->buildCampus(
            $this->buildCampusId($dataCampus['id']),
            $this->buildCampusInstitutionId($dataCampus['institutionId']),
            $this->buildCampusName($dataCampus['name'])
        );

        $campus->address()->setValue($dataCampus['address']);
        $campus->phone()->setValue($dataCampus['phone']);
        $campus->email()->setValue($dataCampus['email']);
        $campus->observations()->setValue($dataCampus['observations']);
        $campus->state()->setValue($dataCampus['state']);

        if (isset($dataCampus['createdAt'])) {
            $campus->createdAt()->setValue($this->getDateTime($dataCampus['createdAt']));
        }

        if (isset($dataCampus['updatedAt'])) {
            $campus->updatedAt()->setValue($this->getDateTime($dataCampus['updatedAt']));
        }

        return $campus;
    }

    public function buildCampusId(?int $id = null): CampusId
    {
        return new CampusId($id);
    }

    public function buildCampusInstitutionId(int $id): CampusInstitutionId
    {
        return new CampusInstitutionId($id);
    }

    public function buildCampusName(string $name): CampusName
    {
        return new CampusName($name);
    }

    public function buildCampusAddress(?string $address = null): CampusAddress
    {
        return new CampusAddress($address);
    }

    public function buildCampusPhone(?string $phone = null): CampusPhone
    {
        return new CampusPhone($phone);
    }

    public function buildCampusEmail(?string $email = null): CampusEmail
    {
        return new CampusEmail($email);
    }

    public function buildCampusObservations(?string $observations = null): CampusObservations
    {
        return new CampusObservations($observations);
    }

    public function buildCampusSearch(?string $search = null): CampusSearch
    {
        return new CampusSearch($search);
    }

    /**
     * @throws \Exception
     */
    public function buildCampusState(int $state): CampusState
    {
        return new CampusState($state);
    }

    public function buildCampusCreatedAt(\DateTime $dateTime): CampusCreatedAt
    {
        return new CampusCreatedAt($dateTime);
    }

    public function buildCampusUpdatedAt(?\DateTime $dateTime = null): CampusUpdatedAt
    {
        return new CampusUpdatedAt($dateTime);
    }

    public function buildCampusCollection(Campus ...$campus): CampusCollection
    {
        return new CampusCollection($campus);
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $dateTime): \DateTime
    {
        return new \DateTime($dateTime);
    }
}
