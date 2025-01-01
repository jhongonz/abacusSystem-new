<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 07:32:05
 */

namespace Core\Institution\Application\Factory;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
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

class InstitutionFactory implements InstitutionFactoryContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function buildInstitutionFromArray(array $data): Institution
    {
        /** @var array{
         *     id: int,
         *     name: string,
         *     shortname: string,
         *     code: string,
         *     observations: string,
         *     state: int,
         *     createdAt: string|null,
         *     updatedAt: string|null,
         *     address: string,
         *     phone: string,
         *     email: string,
         *     logo: string|null
         * } $dataInstitution
         */
        $dataInstitution = $data[Institution::TYPE];

        $institution = $this->buildInstitution(
            $this->buildInstitutionId($dataInstitution['id']),
            $this->buildInstitutionName($dataInstitution['name'])
        );

        $institution->shortname()->setValue($dataInstitution['shortname']);
        $institution->code()->setValue($dataInstitution['code']);
        $institution->observations()->setValue($dataInstitution['observations']);
        $institution->state()->setValue($dataInstitution['state']);
        $institution->address()->setValue($dataInstitution['address']);
        $institution->phone()->setValue($dataInstitution['phone']);
        $institution->email()->setValue($dataInstitution['email']);

        if (isset($dataInstitution['logo'])) {
            $institution->logo()->setValue($dataInstitution['logo']);
        }

        if (isset($dataInstitution['createdAt'])) {
            $institution->createdAt()->setValue($this->getDateTime($dataInstitution['createdAt']));
        }

        if (isset($dataInstitution['updatedAt'])) {
            $institution->updatedAt()->setValue($this->getDateTime($dataInstitution['updatedAt']));
        }

        return $institution;
    }

    public function buildInstitution(InstitutionId $id, InstitutionName $name): Institution
    {
        return new Institution($id, $name);
    }

    public function buildInstitutionId(?int $id = null): InstitutionId
    {
        return new InstitutionId($id);
    }

    public function buildInstitutionCode(?string $code = null): InstitutionCode
    {
        return new InstitutionCode($code);
    }

    public function buildInstitutionName(string $name): InstitutionName
    {
        return new InstitutionName($name);
    }

    public function buildInstitutionShortname(?string $shortname = null): InstitutionShortname
    {
        return new InstitutionShortname($shortname);
    }

    public function buildInstitutionLogo(?string $logo = null): InstitutionLogo
    {
        return new InstitutionLogo($logo);
    }

    public function buildInstitutionObservations(?string $observations = null): InstitutionObservations
    {
        return new InstitutionObservations($observations);
    }

    /**
     * @throws \Exception
     */
    public function buildInstitutionState(int $state = ValueObjectStatus::STATE_NEW): InstitutionState
    {
        return new InstitutionState($state);
    }

    public function buildInstitutionSearch(?string $search = null): InstitutionSearch
    {
        return new InstitutionSearch($search);
    }

    public function buildInstitutionCreatedAt(\DateTime $dateTime = new \DateTime('now')): InstitutionCreatedAt
    {
        return new InstitutionCreatedAt($dateTime);
    }

    public function buildInstitutionUpdatedAt(?\DateTime $dateTime = null): InstitutionUpdatedAt
    {
        return new InstitutionUpdatedAt($dateTime);
    }

    public function buildInstitutions(Institution ...$institutions): Institutions
    {
        return new Institutions($institutions);
    }

    public function buildInstitutionAddress(?string $address = null): InstitutionAddress
    {
        return new InstitutionAddress($address);
    }

    public function buildInstitutionPhone(string $phone): InstitutionPhone
    {
        return new InstitutionPhone($phone);
    }

    public function buildInstitutionEmail(?string $email = null): InstitutionEmail
    {
        return new InstitutionEmail($email);
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $dateTime): \DateTime
    {
        return new \DateTime($dateTime);
    }
}
