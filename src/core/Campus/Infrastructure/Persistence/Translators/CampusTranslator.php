<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 13:49:19
 */

namespace Core\Campus\Infrastructure\Persistence\Translators;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Infrastructure\Persistence\Eloquent\Model\Campus as CampusModel;

class CampusTranslator
{
    private CampusModel $model;

    /**
     * @var array<int<0, max>, int>
     */
    private array $collection = [];

    public function __construct(
        private readonly CampusFactoryContract $campusFactory,
    ) {
    }

    public function setModel(CampusModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function toDomain(): Campus
    {
        $campus = $this->campusFactory->buildCampus(
            $this->campusFactory->buildCampusId($this->model->id()),
            $this->campusFactory->buildCampusInstitutionId($this->model->institutionId()),
            $this->campusFactory->buildCampusName($this->model->name())
        );

        $campus->address()->setValue($this->model->address());
        $campus->email()->setValue($this->model->email());
        $campus->phone()->setValue($this->model->phone());
        $campus->observations()->setValue($this->model->observations());
        $campus->state()->setValue($this->model->state());
        $campus->search()->setValue($this->model->search());

        if (!is_null($this->model->createdAt())) {
            $campus->createdAt()->setValue($this->model->createdAt());
        }

        if (!is_null($this->model->updatedAt())) {
            $campus->updatedAt()->setValue($this->model->updatedAt());
        }

        return $campus;
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

    public function toDomainCollection(): CampusCollection
    {
        $campusCollection = new CampusCollection();
        foreach ($this->collection as $id) {
            $campusCollection->addId($id);
        }

        return $campusCollection;
    }
}
