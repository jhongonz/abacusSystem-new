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

class ChainCampusRepository extends AbstractChainRepository implements CampusRepositoryContract
{
    private const FUNCTION_NAME = 'persistCampus';

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAME;
    }

    /**
     * @throws \Throwable
     */
    public function find(CampusId $id): ?Campus
    {
        try {
            /** @var Campus|null $result */
            $result = $this->read(__FUNCTION__, $id);

            return $result;
        } catch (\Exception $exception) {
            throw new CampusNotFoundException(sprintf('Campus not found by id %s', $id->value()));
        }
    }

    /**
     * @throws CampusCollectionNotFoundException
     * @throws \Throwable
     */
    public function getAll(CampusInstitutionId $id, array $filters = []): ?CampusCollection
    {
        $this->canPersist = false;

        try {
            /** @var CampusCollection|null $result */
            $result = $this->read(__FUNCTION__, $id, $filters);

            return $result;
        } catch (\Exception $exception) {
            throw new CampusCollectionNotFoundException('Campus collection not found');
        }
    }

    /**
     * @throws \Exception
     */
    public function delete(CampusId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    /**
     * @throws \Exception
     */
    public function persistCampus(Campus $campus): Campus
    {
        /** @var Campus $result */
        $result = $this->write(__FUNCTION__, $campus);

        return $result;
    }
}
