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

class InstitutionTranslator
{
    private InstitutionModel $institution;

    /** @var array<int<0, max>, int> */
    private array $collection = [];

    public function __construct(
        private readonly InstitutionFactoryContract $institutionFactory,
    ) {
    }

    public function setModel(InstitutionModel $model): self
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
            $this->institutionFactory->buildInstitutionName($this->institution->name() ?? '')
        );

        /** @var string $shortname */
        $shortname = $this->institution->shortname();
        $institution->shortname()->setValue($shortname);

        /** @var string $code */
        $code = $this->institution->code();
        $institution->code()->setValue($code);

        /** @var string|null $logo */
        $logo = $this->institution->logo();
        if (!is_null($logo)) {
            $institution->logo()->setValue($logo);
        }

        $institution->state()->setValue($this->institution->state());

        /** @var string $observations */
        $observations = $this->institution->observations();
        $institution->observations()->setValue($observations);

        /** @var string $address */
        $address = $this->institution->address();
        $institution->address()->setValue($address);

        /** @var string $phone */
        $phone = $this->institution->phone();
        $institution->phone()->setValue($phone);

        /** @var string $email */
        $email = $this->institution->email();
        $institution->email()->setValue($email);

        $institution->search()->setValue($this->institution->search());

        $createdAt = $this->institution->createdAt();
        if (!is_null($createdAt)) {
            $institution->createdAt()->setValue($createdAt);
        }

        $updatedAt = $this->institution->updatedAt();
        if (!is_null($updatedAt)) {
            $institution->updatedAt()->setValue($updatedAt);
        }

        return $institution;
    }

    /**
     * @param array<int<0, max>, int> $collection
     *
     * @return $this
     */
    public function setCollection(array $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function toDomainCollection(): Institutions
    {
        $institutions = new Institutions();
        foreach ($this->collection as $id) {
            $institutions->addId($id);
        }

        return $institutions;
    }
}
