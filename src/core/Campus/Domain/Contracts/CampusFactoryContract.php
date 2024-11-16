<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 13:01:08
 */

namespace Core\Campus\Domain\Contracts;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
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

interface CampusFactoryContract
{
    public function buildCampus(
        CampusId $id,
        CampusInstitutionId $institutionId,
        CampusName $name,
    ): Campus;

    /**
     * @param array<string, mixed> $data
     */
    public function buildCampusFromArray(array $data): Campus;

    public function buildCampusId(?int $id = null): CampusId;

    public function buildCampusInstitutionId(int $id): CampusInstitutionId;

    public function buildCampusName(string $name): CampusName;

    public function buildCampusAddress(?string $address = null): CampusAddress;

    public function buildCampusPhone(?string $phone = null): CampusPhone;

    public function buildCampusEmail(?string $email = null): CampusEmail;

    public function buildCampusObservations(?string $observations = null): CampusObservations;

    public function buildCampusSearch(?string $search = null): CampusSearch;

    public function buildCampusState(int $state): CampusState;

    public function buildCampusCreatedAt(\DateTime $dateTime): CampusCreatedAt;

    public function buildCampusUpdatedAt(?\DateTime $dateTime = null): CampusUpdatedAt;

    public function buildCampusCollection(Campus ...$campus): CampusCollection;
}
