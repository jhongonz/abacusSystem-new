<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 15:52:28
 */

namespace Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Exceptions\CampusNotFoundException;
use Core\Campus\Exceptions\CampusPersistException;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Exception;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class RedisCampusRepository implements ChainPriority, CampusRepositoryContract
{
    private const PRIORITY_DEFAULT = 100;
    private const CAMPUS_KEY_FORMAT = '%s::%s';

    private int $priority;
    private string $keyPrefix;
    private CampusFactoryContract $campusFactory;
    private CampusDataTransformerContract $campusDataTransformer;
    private LoggerInterface $logger;

    public function __construct(
        CampusFactoryContract $campusFactory,
        CampusDataTransformerContract $campusDataTransformer,
        LoggerInterface $logger,
        string $keyPrefix = 'campus',
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->campusFactory = $campusFactory;
        $this->campusDataTransformer = $campusDataTransformer;
        $this->logger = $logger;
        $this->keyPrefix = $keyPrefix;
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
     */
    public function find(CampusId $id): ?Campus
    {
        try {
            $data = Redis::get($this->campusKey($id));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new CampusNotFoundException(sprintf('Campus not found by id %s', $id->value()));
        }

        if (isset($data)) {
            $dataArray = json_decode($data, true);

            return $this->campusFactory->buildCampusFromArray($dataArray);
        }

        return null;
    }

    public function getAll(CampusInstitutionId $id, array $filters = []): ?CampusCollection
    {
        return null;
    }

    public function delete(CampusId $id): void
    {
        Redis::delete($this->campusKey($id));
    }

    /**
     * @throws CampusPersistException
     */
    public function persistCampus(Campus $campus): Campus
    {
        $campusKey = $this->campusKey($campus->id());
        $campusData = $this->campusDataTransformer->write($campus)->read();

        try {
            Redis::set($campusKey, json_encode($campusData));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new CampusPersistException(sprintf('It could not persist Campus with key %s in redis', $campusKey));
        }

        return $campus;
    }

    private function campusKey(CampusId $id): string
    {
        return sprintf(self::CAMPUS_KEY_FORMAT, $this->keyPrefix, $id->value());
    }
}
