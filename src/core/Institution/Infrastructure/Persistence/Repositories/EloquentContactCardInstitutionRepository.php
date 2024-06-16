<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-16 10:32:32
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
use Core\Institution\Domain\Contracts\ContactCardInstitutionRepositoryContract;
use Core\Institution\Domain\ValueObjectsContactCard\ContactId;
use Core\Institution\Domain\ValueObjectsContactCard\ContactInstitutionId;
use Core\Institution\Exceptions\ContactCardInstitutionNotFoundException;
use Core\Institution\Exceptions\ContactCardsInstitutionNotFoundException;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\InstitutionContactCard as ContactCardModel;
use Core\Institution\Infrastructure\Persistence\Translators\ContactCardInstitutionTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;

class EloquentContactCardInstitutionRepository implements ContactCardInstitutionRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;
    private ContactCardModel $model;
    private ContactCardInstitutionTranslator $cardInstitutionTranslator;
    private DatabaseManager $databaseManager;
    private int $priority;

    public function __construct(
        ContactCardModel $model,
        ContactCardInstitutionTranslator $cardInstitutionTranslator,
        DatabaseManager $databaseManager,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->model = $model;
        $this->cardInstitutionTranslator = $cardInstitutionTranslator;
        $this->databaseManager = $databaseManager;
        $this->priority = $priority;
    }

    /**
     * @throws ContactCardInstitutionNotFoundException
     * @throws Exception
     */
    public function find(ContactId $id): ?ContactCard
    {
        $builder = $this->databaseManager->table($this->getTable())
            ->where('card_id', $id->value())
            ->where('card_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();
        if (is_null($data)) {
            throw new ContactCardInstitutionNotFoundException(
                sprintf('Institution not found with id %s', $id->value())
            );
        }

        $contactCardModel = $this->updateAttributesModel((array) $data);

        return $this->cardInstitutionTranslator->setModel($contactCardModel)->toDomain();
    }

    /**
     * @throws ContactCardsInstitutionNotFoundException
     */
    public function getAll(ContactInstitutionId $institutionId, array $filters = []): ?ContactCards
    {
        $builder = $this->databaseManager->table($this->getTable())
            ->where('card_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }

        $contactCardCollection = $builder->get(['card_id']);
        if (is_null($contactCardCollection)) {
            throw new ContactCardsInstitutionNotFoundException('Contact cards not found');
        }

        $collection = [];
        foreach ($contactCardCollection as $item) {
            $contactCardModel = $this->updateAttributesModel((array) $item);
            $collection[] = $contactCardModel->id();
        }

        $contactCards = $this->cardInstitutionTranslator->setCollection($collection)->toDomainCollection();
        $contactCards->setFilters($filters);

        return $contactCards;
    }

    /**
     * @throws ContactCardInstitutionNotFoundException
     */
    public function delete(ContactId $id): void
    {
        $builder = $this->databaseManager->table($this->getTable())
            ->where('card_id', $id->value());
        $data = $builder->first();

        if (is_null($data)) {
            throw new ContactCardInstitutionNotFoundException(
                sprintf('Institution not found with id %s', $id->value())
            );
        }

        $cardModel = $this->updateAttributesModel((array) $data);
        $cardModel->changeState(ValueObjectStatus::STATE_DELETE);
        $cardModel->changeDeletedAt(new \DateTime);

        $builder->update($cardModel->toArray());
    }

    public function persisContactCard(ContactCard $contactCard): ContactCard
    {
        $contactCardModel = $this->domainToModel($contactCard);
        $contactCardId = $contactCardModel->id();
        $dataModel = $contactCardModel->toArray();

        $builder = $this->databaseManager->table($this->getTable());

        if (is_null($contactCardId)) {
            $contactCardId = $builder->insertGetId($dataModel);
            $contactCard->id()->setValue($contactCardId);
        } else {
            $builder->where('card_id', $contactCardId);
            $builder->update($dataModel);
        }

        return $contactCard;
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

    private function getTable(): string
    {
        return $this->model->getTable();
    }

    private function updateAttributesModel(array $data = []): ContactCardModel
    {
        $this->model->fill($data);
        return $this->model;
    }

    private function domainToModel(ContactCard $domain): ContactCardModel
    {
        $builder = $this->databaseManager->table($this->getTable());
        $builder->where('card_id', $domain->id()->value());
        $data = $builder->first();

        $model = $this->updateAttributesModel((array) $data);
        $model->changeId($domain->id()->value());
        $model->changeInstitutionId($domain->institutionId()->value());
        $model->changePhone($domain->phone()->value());
        $model->changeEmail($domain->email()->value());
        $model->changeContactPerson($domain->person()->value());
        $model->changeContactDefault($domain->contactDefault()->value());
        $model->changeObservations($domain->observations()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeState($domain->state()->value());
        $model->changeCreatedAt($domain->createdAt()->value());
        $model->changeUpdatedAt($domain->updatedAt()->value());

        return $model;
    }
}
