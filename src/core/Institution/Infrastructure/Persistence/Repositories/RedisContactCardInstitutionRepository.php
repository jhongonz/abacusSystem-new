<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-16 09:32:35
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
use Core\Institution\Domain\Contracts\ContactCardInstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\ContactCardInstitutionFactoryContract;
use Core\Institution\Domain\Contracts\ContactCardInstitutionRepositoryContract;
use Core\Institution\Domain\ValueObjectsContactCard\ContactId;
use Core\Institution\Domain\ValueObjectsContactCard\ContactInstitutionId;
use Core\Institution\Exceptions\ContactCardInstitutionNotFoundException;
use Core\Institution\Exceptions\ContactCardInstitutionPersistException;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use DateTime;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class RedisContactCardInstitutionRepository implements ContactCardInstitutionRepositoryContract, ChainPriority
{
    /** @var int */
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const CONTACT_CARD_KEY_FORMAT = '%s::%s';
    private int $priority;
    private string $keyPrefix;
    private ContactCardInstitutionFactoryContract $cardInstitutionFactory;
    private ContactCardInstitutionDataTransformerContract $cardInstitutionDataTransformer;
    private LoggerInterface $logger;

    public function __construct(
        ContactCardInstitutionFactoryContract $cardInstitutionFactory,
        ContactCardInstitutionDataTransformerContract $cardInstitutionDataTransformer,
        LoggerInterface $logger,
        string $keyPrefix = 'contact-card-institution',
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->cardInstitutionFactory = $cardInstitutionFactory;
        $this->cardInstitutionDataTransformer = $cardInstitutionDataTransformer;
        $this->logger = $logger;
        $this->keyPrefix = $keyPrefix;
        $this->priority = $priority;
    }

    /**
     * @throws ContactCardInstitutionNotFoundException
     * @throws \Exception
     */
    public function find(ContactId $id): ?ContactCard
    {
        try {
            $data = Redis::get($this->contactCardKey($id));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new ContactCardInstitutionNotFoundException('Contact card not found by id '. $id->value());
        }

        if (isset($data)) {
            $dataArray = json_decode($data, true);
            $dataArray['createdAt'] = new DateTime($dataArray['createdAt']['date']);

            if (isset($dataArray['updatedAt'])) {
                $dataArray['updatedAt'] = new DateTime($dataArray['updatedAt']['date']);
            }

            return $this->cardInstitutionFactory->buildContactCardFromArray($dataArray);
        }

        return null;
    }

    public function getAll(ContactInstitutionId $institutionId, array $filters = []): ?ContactCards
    {
        return null;
    }

    public function delete(ContactId $id): void
    {
        Redis::delete($this->contactCardKey($id));
    }

    /**
     * @throws ContactCardInstitutionPersistException
     */
    public function persisContactCard(ContactCard $contactCard): ContactCard
    {
        $contactCardData = $this->cardInstitutionDataTransformer->write($contactCard)->read();
        $contactCardKey = $this->contactCardKey($contactCard->id());

        try {
            Redis::set($contactCardKey, json_encode($contactCardData));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new ContactCardInstitutionPersistException(
                sprintf('It could not persis contact card institution with key %s in redis', $contactCardKey)
            );
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

    private function contactCardKey(ContactId $id): string
    {
        return sprintf(self::CONTACT_CARD_KEY_FORMAT, $this->keyPrefix, $id->value());
    }
}
