<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 22:00:22
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Exceptions\InstitutionNotFoundException;
use Core\Institution\Exceptions\InstitutionsNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Exception;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class ChainInstitutionRepository extends AbstractChainRepository implements InstitutionRepositoryContract
{
    private const FUNCTION_NAMES = [
        Institution::class => 'persistInstitution',
        Institutions::class => '',
    ];

    private string $domainToPersist;

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws Throwable
     */
    public function find(InstitutionId $id): ?Institution
    {
        $this->domainToPersist = Institution::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new InstitutionNotFoundException('Institution not found by id '. $id->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function getAll(array $filters = []): Institutions
    {
        $this->domainToPersist = Institutions::class;

        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new InstitutionsNotFoundException('Institutions not found');
        }
    }

    public function delete(InstitutionId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    public function persistInstitution(Institution $institution): Institution
    {
        return $this->write(__FUNCTION__, $institution);
    }
}
