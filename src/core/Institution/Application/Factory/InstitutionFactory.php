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
use DateTime;
use Exception;

class InstitutionFactory implements InstitutionFactoryContract
{
    /**
     * @throws Exception
     */
    public function buildInstitutionFromArray(array $data): Institution
    {
        $data = $data[Institution::TYPE];
        $institution = $this->buildInstitution(
            $this->buildInstitutionId($data['id']),
            $this->buildInstitutionName($data['name'])
        );

        $institution->setShortname(
            $this->buildInstitutionShortname($data['shortname'])
        );

        $institution->setCode(
            $this->buildInstitutionCode($data['code'])
        );

        $institution->setObservations(
            $this->buildInstitutionObservations($data['observations'])
        );

        $institution->setState(
            $this->buildInstitutionState($data['state'])
        );

        if (isset($data['createdAt'])) {
            $institution->setCreatedAt(
                $this->buildInstitutionCreatedAt($this->getDateTime($data['createdAt']))
            );
        }

        $institution->setAddress(
            $this->buildInstitutionAddress($data['address'])
        );

        $institution->setPhone(
            $this->buildInstitutionPhone($data['phone'])
        );

        $institution->setEmail(
            $this->buildInstitutionEmail($data['email'])
        );

        if (isset($data['logo'])) {
            $institution->setLogo(
                $this->buildInstitutionLogo($data['logo'])
            );
        }

        if (isset($data['updatedAt'])) {
            $institution->setUpdatedAt(
                $this->buildInstitutionUpdatedAt($this->getDateTime($data['updatedAt']))
            );
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
     * @throws Exception
     */
    public function buildInstitutionState(int $state = null): InstitutionState
    {
        return new InstitutionState($state);
    }

    public function buildInstitutionSearch(?string $search = null): InstitutionSearch
    {
        return new InstitutionSearch($search);
    }

    public function buildInstitutionCreatedAt(?DateTime $dateTime = null): InstitutionCreatedAt
    {
        return new InstitutionCreatedAt($dateTime);
    }

    public function buildInstitutionUpdatedAt(?DateTime $dateTime = null): InstitutionUpdatedAt
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
     * @throws Exception
     */
    private function getDateTime(string $dateTime): DateTime
    {
        return new DateTime($dateTime);
    }
}
