<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:58:12
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Exceptions\InstitutionNotFoundException;
use Core\Institution\Exceptions\InstitutionsNotFoundException;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution as InstitutionModel;
use Core\Institution\Infrastructure\Persistence\Translators\InstitutionTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;

class EloquentInstitutionRepository implements InstitutionRepositoryContract, ChainPriority
{
    /** @var int */
    private const PRIORITY_DEFAULT = 50;

    public function __construct(
        private readonly InstitutionModel $model,
        private readonly InstitutionTranslator $institutionTranslator,
        private readonly DatabaseManager $databaseManager,
        private int $priority = self::PRIORITY_DEFAULT
    ) {
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function changePriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @throws InstitutionNotFoundException
     * @throws Exception
     */
    public function find(InstitutionId $id): ?Institution
    {
        $builder = $this->databaseManager->table($this->getTable())
            ->where('inst_id', $id->value())
            ->where('inst_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();

        if (is_null($data)) {
            throw new InstitutionNotFoundException(
                sprintf('Institution not found with id %s', $id->value())
            );
        }

        $institutionModel = $this->updateAttributesModelInstitution((array) $data);
        return $this->institutionTranslator->setModel($institutionModel)->toDomain();
    }

    /**
     * @throws InstitutionsNotFoundException
     */
    public function getAll(array $filters = []): ?Institutions
    {
        $builder = $this->databaseManager->table($this->getTable())
            ->where('inst_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }

        $institutionCollection = $builder->get(['inst_id']);
        if (is_null($institutionCollection)) {
            throw new InstitutionsNotFoundException('Institutions not found');
        }

        $collection = [];
        foreach ($institutionCollection as $item) {
            $institutionModel = $this->updateAttributesModelInstitution((array) $item);
            $collection[] = $institutionModel->id();
        }

        $institutions = $this->institutionTranslator->setCollection($collection)->toDomainCollection();
        $institutions->setFilters($filters);

        return $institutions;
    }

    /**
     * @throws InstitutionNotFoundException
     * @throws Exception
     */
    public function delete(InstitutionId $id): void
    {
        $builder = $this->databaseManager->table($this->getTable())
            ->where('inst_id', $id->value());
        $data = $builder->first();

        if (is_null($data)) {
            throw new InstitutionNotFoundException(
                sprintf('Institution not found with id %s', $id->value())
            );
        }

        $institutionModel = $this->updateAttributesModelInstitution((array) $data);
        $institutionModel->changeState(ValueObjectStatus::STATE_DELETE);
        $institutionModel->changeDeletedAt($this->getDateTime());

        $builder->update($institutionModel->toArray());
    }

    /**
     * @throws Exception
     */
    public function persistInstitution(Institution $institution): Institution
    {
        $institutionModel = $this->domainToModel($institution);
        $institutionId = $institutionModel->id();
        $dataModel = $institutionModel->toArray();

        $builder = $this->databaseManager->table($this->getTable());

        if (is_null($institutionId)) {
            $dataModel['created_at'] = $this->getDateTime();

            $institutionId = $builder->insertGetId($dataModel);
            $institution->id()->setValue($institutionId);
            $institution->createdAt()->setValue($dataModel['created_at']);
        } else {
            $dataModel['updated_at'] = $this->getDateTime();
            $institution->updatedAt()->setValue($dataModel['updated_at']);

            $builder->where('inst_id', $institutionId);
            $builder->update($dataModel);
        }

        return $institution;
    }

    private function domainToModel(Institution $domain): InstitutionModel
    {
        $builder = $this->databaseManager->table($this->getTable());
        $builder->where('inst_id', $domain->id()->value());
        $data = $builder->first();

        $model = $this->updateAttributesModelInstitution((array) $data);
        $model->changeId($domain->id()->value());
        $model->changeName($domain->name()->value());
        $model->changeShortname($domain->shortname()->value());
        $model->changeCode($domain->code()->value());
        $model->changeLogo($domain->logo()->value());
        $model->changeObservations($domain->observations()->value());
        $model->changeAddress($domain->address()->value());
        $model->changePhone($domain->phone()->value());
        $model->changeEmail($domain->email()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeState($domain->state()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    private function updateAttributesModelInstitution(array $data = []): InstitutionModel
    {
        $this->model->fill($data);

        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * @throws Exception
     */
    private function getDateTime(string $datetime = 'now'): \DateTime
    {
        return new \DateTime($datetime);
    }
}
