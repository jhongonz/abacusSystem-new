<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 14:09:03
 */

namespace Core\Institution\Domain;

use Core\Institution\Domain\ValueObjects\InstitutionAddress;
use Core\Institution\Domain\ValueObjects\InstitutionCode;
use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use Core\Institution\Domain\ValueObjects\InstitutionName;
use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use Core\Institution\Domain\ValueObjects\InstitutionSearch;
use Core\Institution\Domain\ValueObjects\InstitutionShortname;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;

class Institution
{
    public const TYPE = 'institution';
    private InstitutionId $id;
    private InstitutionCode $code;
    private InstitutionName $name;
    private InstitutionShortname $shortname;
    private InstitutionLogo $logo;
    private InstitutionObservations $observations;
    private InstitutionAddress $address;
    private InstitutionState $state;
    private InstitutionSearch $search;
    private InstitutionCreatedAt $createdAt;
    private InstitutionUpdatedAt $updatedAt;

    public function __construct(
        InstitutionId $id,
        InstitutionName $name,
        InstitutionShortname $shortname = new InstitutionShortname,
        InstitutionCode $code = new InstitutionCode,
        InstitutionState $state = new InstitutionState,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->shortname = $shortname;
        $this->code = $code;
        $this->state = $state;

        $this->logo = new InstitutionLogo;
        $this->search = new InstitutionSearch;
        $this->observations = new InstitutionObservations;
        $this->createdAt = new InstitutionCreatedAt;
        $this->updatedAt = new InstitutionUpdatedAt;
    }

    public function id(): InstitutionId
    {
        return $this->id;
    }

    public function setId(InstitutionId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function name(): InstitutionName
    {
        return $this->name;
    }

    public function setName(InstitutionName $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function shortname(): InstitutionShortname
    {
        return $this->shortname;
    }

    public function setShortname(InstitutionShortname $shortname): self
    {
        $this->shortname = $shortname;
        return $this;
    }

    public function code(): InstitutionCode
    {
        return $this->code;
    }

    public function setCode(InstitutionCode $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function logo(): InstitutionLogo
    {
        return $this->logo;
    }

    public function setLogo(InstitutionLogo $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function observations(): InstitutionObservations
    {
        return $this->observations;
    }

    public function setObservations(InstitutionObservations $observations): self
    {
        $this->observations = $observations;
        return $this;
    }

    public function address(): InstitutionAddress
    {
        return $this->address;
    }

    public function setAddress(InstitutionAddress $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function state(): InstitutionState
    {
        return $this->state;
    }

    public function setState(InstitutionState $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function search(): InstitutionSearch
    {
        return $this->search;
    }

    public function setSearch(InstitutionSearch $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function createdAt(): InstitutionCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(InstitutionCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updatedAt(): InstitutionUpdatedAt
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(InstitutionUpdatedAt $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function refreshSearch(): self
    {
        $data = [
            $this->code->value(),
            $this->name->value(),
            $this->shortname->value(),
            $this->observations->value()
        ];

        $this->search->setValue(implode(' ', $data));
        return $this;
    }
}
