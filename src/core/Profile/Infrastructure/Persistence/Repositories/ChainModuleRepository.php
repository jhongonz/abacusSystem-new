<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Exception;
use Throwable;

class ChainModuleRepository extends AbstractChainRepository implements ModuleRepositoryContract
{
    private const FUNCTION_NAMES = [
        Module::class => 'persistModule',
        Modules::class => 'persistModules',
    ];

    private string $domainToPersist;
    private bool $deleteSource = false;

    /**
     * @throws Throwable
     * @throws ModuleNotFoundException
     */
    public function find(ModuleId $id): null|Module
    {
        $this->domainToPersist = Module::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ModuleNotFoundException('Module not found by id '. $id->value());
        }
    }

    public function persistModule(Module $module): Module
    {
        return $this->writeChain(__FUNCTION__, $module);
    }

    function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Throwable
     */
    public function getAll(array $filters = []): null|Modules
    {
        $this->domainToPersist = Modules::class;

        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new ModulesNotFoundException('Modules no found');
        }
    }

    public function persistModules(Modules $modules): Modules
    {
        return $this->write(__FUNCTION__, $modules);
    }

    /**
     * @throws Throwable
     * @throws ModuleNotFoundException
     */
    public function deleteModule(ModuleId $id): void
    {
        $this->deleteSource = true;

        try {
            $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new ModuleNotFoundException($exception->getMessage());
        }
    }

    function functionNameDelete(): bool
    {
        return $this->deleteSource;
    }
}
