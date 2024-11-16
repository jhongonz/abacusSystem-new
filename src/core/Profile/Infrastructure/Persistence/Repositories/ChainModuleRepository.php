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
            /** @var Module|null $result */
            $result = $this->read(__FUNCTION__, $id);

            return $result;
        } catch (Exception $exception) {
            throw new ModuleNotFoundException('Module not found by id '.$id->value());
        }
    }

    /**
     * @throws Exception
     */
    public function persistModule(Module $module): Module
    {
        /** @var Module $result */
        $result = $this->write(__FUNCTION__, $module);

        return $result;
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Throwable
     */
    public function getAll(array $filters = []): ?Modules
    {
        $this->canPersist = false;

        try {
            /** @var Modules|null $result */
            $result = $this->read(__FUNCTION__, $filters);

            return $result;
        } catch (Exception $exception) {
            throw new ModulesNotFoundException('Modules not found');
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
