<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 22:00:22
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
use Core\Institution\Domain\Contracts\ContactCardInstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjectsContactCard\ContactId;
use Core\Institution\Domain\ValueObjectsContactCard\ContactInstitutionId;
use Core\Institution\Exceptions\ContactCardInstitutionNotFoundException;
use Core\Institution\Exceptions\ContactCardsInstitutionNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Exception;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class ChainContactCardInstitutionRepository extends AbstractChainRepository implements ContactCardInstitutionRepositoryContract
{
    private const FUNCTION_NAMES = [
        Institution::class => 'persistContactCard',
        Institutions::class => 'persistContactCards',
    ];

    private string $domainToPersist;

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws Throwable
     */
    public function find(ContactId $id): ?ContactCard
    {
        $this->domainToPersist = ContactCard::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ContactCardInstitutionNotFoundException('Contact Card not found by id '. $id->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function getAll(ContactInstitutionId $institutionId, array $filters = []): ?ContactCards
    {
        $this->domainToPersist = ContactCards::class;

        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new ContactCardsInstitutionNotFoundException('Contact Cards not found');
        }
    }

    public function delete(ContactId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    public function persisContactCard(ContactCard $contactCard): ContactCard
    {
        return $this->write(__FUNCTION__, $contactCard);
    }
}
