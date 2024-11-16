<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:57:42
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Exceptions\InstitutionPersistException;
use Core\Institution\Exceptions\InstitutionsNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Exception;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class RedisInstitutionRepository implements InstitutionRepositoryContract, ChainPriority
{
    /** @var int */
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const INSTITUTION_KEY_FORMAT = '%s::%s';

    public function __construct(
        private readonly InstitutionFactoryContract $institutionFactory,
        private readonly InstitutionDataTransformerContract $dataTransformer,
        private readonly LoggerInterface $logger,
        private readonly string $keyPrefix = 'institution',
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
     * @throws InstitutionsNotFoundException
     * @throws Exception
     */
    public function find(InstitutionId $id): ?Institution
    {
        try {
            /** @var string $data */
            $data = Redis::get($this->institutionKey($id));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new InstitutionsNotFoundException('Institution not found by id '. $id->value());
        }

        if (! empty($data)) {
            /** @var array<string, mixed> $dataArray */
            $dataArray = json_decode($data, true);

            /**@var Institution*/
            return $this->institutionFactory->buildInstitutionFromArray($dataArray);
        }

        return null;
    }

    public function getAll(array $filters = []): ?Institutions
    {
        return null;
    }

    public function delete(InstitutionId $id): void
    {
        Redis::delete($this->institutionKey($id));
    }

    /**
     * @throws InstitutionPersistException
     */
    public function persistInstitution(Institution $institution): Institution
    {
        $institutionKey = $this->institutionKey($institution->id());
        $institutionData = $this->dataTransformer->write($institution)->read();

        try {
            Redis::set($institutionKey, json_encode($institutionData));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new InstitutionPersistException(
                sprintf('It could not persist Institution with key %s in redis', $institutionKey)
            );
        }

        return $institution;
    }

    private function institutionKey(InstitutionId $id): string
    {
        return sprintf(self::INSTITUTION_KEY_FORMAT, $this->keyPrefix, $id->value());
    }
}
