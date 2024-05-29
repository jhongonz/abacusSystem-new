<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 22:46:46
 */

namespace Core\Institution\Infrastructure\Persistence\Translators;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution as InstitutionModel;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;

class InstitutionTranslator implements TranslatorDomainContract
{
    private InstitutionFactoryContract $institutionFactory;
    private InstitutionModel $institution;
    private array $collection;

    public function __construct(
        InstitutionFactoryContract $institutionFactory
    ) {
        $this->institutionFactory = $institutionFactory;
        $this->collection = [];
    }

    /**
     * @param InstitutionModel $model
     * @return $this
     */
    public function setModel($model): self
    {
        $this->institution = $model;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function toDomain(): Institution
    {
        $institution = $this->institutionFactory->buildInstitution(
            $this->institutionFactory->buildInstitutionId($this->institution->id()),
            $this->institutionFactory->buildInstitutionName($this->institution->name())
        );

        $institution->setShortname($this->institutionFactory->buildInstitutionShortname($this->institution->shortname()));
        $institution->setCode($this->institutionFactory->buildInstitutionCode($this->institution->code()));
        $institution->setLogo($this->institutionFactory->buildInstitutionLogo($this->institution->logo()));
        $institution->setState($this->institutionFactory->buildInstitutionState($this->institution->state()));
        $institution->setObservations($this->institutionFactory->buildInstitutionObservations($this->institution->observations()));
        $institution->setCreatedAt($this->institutionFactory->buildInstitutionCreatedAt($this->institution->createdAt()));
        $institution->setUpdatedAt($this->institutionFactory->buildInstitutionUpdatedAt($this->institution->updatedAt()));
        $institution->setSearch($this->institutionFactory->buildInstitutionSearch($this->institution->search()));

        return $institution;
    }

    public function setCollection(array $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    public function toDomainCollection(): Institutions
    {
        $institutions = new Institutions;
        foreach ($this->collection as $id) {
            $institutions->addId($id);
        }

        return $institutions;
    }
}
