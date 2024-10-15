<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Exception;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class ChainModuleRepository extends AbstractChainRepository implements ModuleRepositoryContract
{
    private const FUNCTION_NAME = 'persistModule';

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAME;
    }

    /**
     * @throws Throwable
     * @throws ModuleNotFoundException
     */
    public function find(ModuleId $id): ?Module
    {
        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ModuleNotFoundException('Module not found by id '.$id->value());
        }
    }

    public function persistModule(Module $module): Module
    {
        return $this->write(__FUNCTION__, $module);
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Throwable
     */
    public function getAll(array $filters = []): ?Modules
    {
        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new ModulesNotFoundException('Modules no found');
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteModule(ModuleId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }
}
