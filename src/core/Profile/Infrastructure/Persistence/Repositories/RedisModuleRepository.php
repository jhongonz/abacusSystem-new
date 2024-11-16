<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulePersistException;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class RedisModuleRepository implements ChainPriority, ModuleRepositoryContract
{
    /** @var int */
    private const PRIORITY_DEFAULT = 100;
    /** @var string */
    private const MODULE_KEY_FORMAT = '%s::%s';

    public function __construct(
        private readonly ModuleFactoryContract $moduleFactory,
        private readonly ModuleDataTransformerContract $moduleDataTransformer,
        private readonly LoggerInterface $logger,
        private int $priority = self::PRIORITY_DEFAULT,
        private readonly string $keyPrefix = 'module',
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
     * @throws ModuleNotFoundException
     * @throws \Exception
     */
    public function find(ModuleId $id): ?Module
    {
        try {
            /** @var string $data */
            $data = Redis::get($this->moduleKey($id));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new ModuleNotFoundException('Module not found by id '.$id->value());
        }

        if (!empty($data)) {
            /** @var array<string, mixed> $dataArray */
            $dataArray = json_decode($data, true);

            /* @var Module */
            return $this->moduleFactory->buildModuleFromArray($dataArray);
        }

        return null;
    }

    /**
     * @throws ModulePersistException
     */
    public function persistModule(Module $module): Module
    {
        $moduleKey = $this->moduleKey($module->id());

        $moduleData = $this->moduleDataTransformer->write($module)->read();
        try {
            Redis::set($moduleKey, json_encode($moduleData));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new ModulePersistException('It could not persist Module with key '.$moduleKey.' in redis');
        }

        return $module;
    }

    public function getAll(array $filters = []): ?Modules
    {
        return null;
    }

    public function deleteModule(ModuleId $id): void
    {
        Redis::delete($this->moduleKey($id));
    }

    private function moduleKey(ModuleId $id): string
    {
        return sprintf(self::MODULE_KEY_FORMAT, $this->keyPrefix, $id->value());
    }
}
