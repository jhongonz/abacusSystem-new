<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 14:22:41
 */

namespace Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Exceptions\CampusCollectionNotFoundException;
use Core\Campus\Exceptions\CampusNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Throwable;

class ChainCampusRepository extends AbstractChainRepository implements CampusRepositoryContract
{
    private const FUNCTION_NAMES = [
        Campus::TYPE => 'persistCampus'
    ];

    private string $domainToPersist;

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws Throwable
     */
    public function find(CampusId $id): ?Campus
    {
        $this->domainToPersist = Campus::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (\Exception $exception) {
            throw new CampusNotFoundException(sprintf('Campus not found by id %s', $id->value()));
        }
    }

    /**
     * @throws CampusCollectionNotFoundException
     * @throws Throwable
     */
    public function getAll(CampusInstitutionId $id, array $filters = []): ?CampusCollection
    {
        try {
            $this->read(__FUNCTION__, $id, $filters);
        } catch (\Exception $exception) {
            throw new CampusCollectionNotFoundException('Campus collection no found');
        }
    }

    public function delete(CampusId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    public function persistCampus(Campus $campus): Campus
    {
        return $this->write(__FUNCTION__, $campus);
    }
}
