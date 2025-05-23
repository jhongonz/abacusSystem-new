<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 07:21:29
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionAddress;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionEmail;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionPhone;
use Core\Institution\Domain\ValueObjects\InstitutionSearch;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use Core\SharedContext\Model\ValueObjectStatus;

interface InstitutionFactoryContract
{
    /**
     * @param array<string, mixed> $data
     */
    public function buildInstitutionFromArray(array $data): Institution;

    public function buildInstitution(InstitutionId $id, InstitutionName $name): Institution;

    public function buildInstitutionId(?int $id = null): InstitutionId;

    public function buildInstitutionCode(?string $code = null): InstitutionCode;

    public function buildInstitutionName(string $name): InstitutionName;

    public function buildInstitutionShortname(?string $shortname = null): InstitutionShortname;

    public function buildInstitutionLogo(?string $logo = null): InstitutionLogo;

    public function buildInstitutionObservations(?string $observations = null): InstitutionObservations;

    public function buildInstitutionAddress(?string $address = null): InstitutionAddress;

    public function buildInstitutionPhone(string $phone): InstitutionPhone;

    public function buildInstitutionEmail(?string $email = null): InstitutionEmail;

    public function buildInstitutionState(int $state = ValueObjectStatus::STATE_NEW): InstitutionState;

    public function buildInstitutionSearch(?string $search = null): InstitutionSearch;

    public function buildInstitutionCreatedAt(\DateTime $dateTime = new \DateTime('now')): InstitutionCreatedAt;

    public function buildInstitutionUpdatedAt(?\DateTime $dateTime = null): InstitutionUpdatedAt;

    public function buildInstitutions(Institution ...$institutions): Institutions;
}
