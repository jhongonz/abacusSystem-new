<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 14:35:23
 */

namespace Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Exceptions\CampusCollectionNotFoundException;
use Core\Campus\Exceptions\CampusNotFoundException;
use Core\Campus\Infrastructure\Persistence\Eloquent\Model\Campus as CampusModel;
use Core\Campus\Infrastructure\Persistence\Translators\CampusTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;

class EloquentCampusRepository implements ChainPriority, CampusRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    private CampusModel $campusModel;
    private CampusTranslator $campusTranslator;
    private DatabaseManager $databaseManager;
    private int $priority;

    public function __construct(
        DatabaseManager $databaseManager,
        CampusTranslator $campusTranslator,
        CampusModel $campusModel,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->databaseManager = $databaseManager;
        $this->campusTranslator = $campusTranslator;
        $this->campusModel = $campusModel;
        $this->priority = $priority;
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
     * @throws CampusNotFoundException
     * @throws Exception
     */
    public function find(CampusId $id): ?Campus
    {
        $builder = $this->databaseManager->table($this->getTable());
        $builder->where('cam_id', $id->value());
        $builder->where('cam_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();
        if (is_null($data)) {
            throw new CampusNotFoundException(sprintf('Campus not found with id %s', $id->value()));
        }
        $campusModel = $this->updateAttributesModel((array) $data);

        return $this->campusTranslator->setModel($campusModel)->toDomain();
    }

    /**
     * @throws CampusCollectionNotFoundException
     */
    public function getAll(CampusInstitutionId $id, array $filters = []): ?CampusCollection
    {
        $builder = $this->databaseManager->table($this->getTable());
        $builder->where('cam__inst_id', $id->value());
        $builder->where('cam_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->campusModel->getSearchField(), $filters['q']);
        }
        $campusCollectionResult = $builder->get(['cam_id']);

        if (empty($campusCollectionResult)) {
            throw new CampusCollectionNotFoundException('Campus collection not found');
        }

        $collection = [];
        foreach ($campusCollectionResult as $item) {
            $campusModel = $this->updateAttributesModel((array) $item);
            $collection[] = $campusModel->id();
        }

        $campusCollection = $this->campusTranslator->setCollection($collection)->toDomainCollection();
        $campusCollection->setFilters($filters);

        return $campusCollection;
    }

    /**
     * @throws CampusNotFoundException
     * @throws Exception
     */
    public function delete(CampusId $id): void
    {
        $builder = $this->databaseManager->table($this->getTable());
        $builder->where('cam_if', $id->value());
        $data = $builder->first();

        if (is_null($data)) {
            throw new CampusNotFoundException(sprintf('Campus not found with id %s', $id->value()));
        }

        $campusModel = $this->updateAttributesModel((array) $data);
        $campusModel->changeState(ValueObjectStatus::STATE_DELETE);
        $campusModel->changeDeletedAt($this->getDateTime());

        $builder->update($campusModel->toArray());
    }

    /**
     * @throws Exception
     */
    public function persistCampus(Campus $campus): Campus
    {
        $campusModel = $this->domainToModel($campus);
        $campusId = $campusModel->id();
        $dataModel = $campusModel->toArray();

        $builder = $this->databaseManager->table($this->getTable());

        if (is_null($campusId)) {
            $campusId = $builder->insertGetId($dataModel);
            $campus->id()->setValue($campusId);
        } else {
            $dataModel['updated_at'] = $this->getDateTime();

            $builder->where('cam_id', $campusId);
            $builder->update($dataModel);
        }

        return $campus;
    }

    private function domainToModel(Campus $domain): CampusModel
    {
        $builder = $this->databaseManager->table($this->getTable());
        $builder->where('cam_id', $domain->id()->value());
        $data = $builder->first();

        $model = $this->updateAttributesModel((array) $data);

        $model->changeId($domain->id()->value());
        $model->changeInstitutionId($domain->institutionId()->value());
        $model->changeName($domain->name()->value());
        $model->changePhone($domain->phone()->value());
        $model->changeEmail($domain->email()->value());
        $model->changeAddress($domain->address()->value());
        $model->changeObservations($domain->observations()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeState($domain->state()->value());
        $model->changeCreatedAt($domain->createdAt()->value());
        $model->changeUpdatedAt($domain->updatedAt()->value());

        return $model;
    }

    private function updateAttributesModel(array $data = []): CampusModel
    {
        $this->campusModel->fill($data);
        return $this->campusModel;
    }

    private function getTable(): string
    {
        return $this->campusModel->getTable();
    }

    /**
     * @throws Exception
     */
    private function getDateTime(string $datetime = 'now'): \DateTime
    {
        return new \DateTime($datetime);
    }
}
