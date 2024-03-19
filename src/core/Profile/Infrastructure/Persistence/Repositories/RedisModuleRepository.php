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
use Exception;
use Illuminate\Support\Facades\Redis;

class RedisModuleRepository implements ModuleRepositoryContract, ChainPriority
{
    /**@var int*/
    private const PRIORITY_DEFAULT = 100;
    /** @var string */
    private const MODULE_KEY_FORMAT = '%s::%s';

    private ModuleFactoryContract $moduleFactory;
    private ModuleDataTransformerContract $moduleDataTransformer;
    private int $priority;
    private string $keyPrefix;

    public function __construct(
        ModuleFactoryContract $moduleFactory,
        ModuleDataTransformerContract $moduleDataTransformer,
        int $priority = self::PRIORITY_DEFAULT,
        string $keyPrefix = 'module',
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->moduleDataTransformer = $moduleDataTransformer;
        $this->priority = $priority;
        $this->keyPrefix = $keyPrefix;
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
     */
    public function find(ModuleId $id): null|Module
    {
        try {
            $data = Redis::get($this->moduleKey($id));
        } catch (Exception $exception) {
            throw new ModuleNotFoundException('Module not found by id '. $id->value());
        }

        if (!is_null($data)) {
            $dataArray = json_decode($data, true);

            /** @var Module */
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
        } catch (Exception $exception) {
            throw new ModulePersistException('It could not persist Module with key '.$moduleKey.' in redis');
        }

        return $module;
    }

    private function moduleKey(ModuleId $id): string
    {
        return sprintf(self::MODULE_KEY_FORMAT, $this->keyPrefix, $id->value());
    }

    public function getAll(array $filters = []): null|Modules
    {
        return null;
    }

    public function persistModules(Modules $modules): Modules
    {
        return $modules;
    }

    public function deleteModule(ModuleId $id): void
    {
        Redis::delete($this->moduleKey($id));
    }
}
