<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use App\Models\Module as ModuleModel;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Exceptions\ModuleDeleteException;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Translators\ModuleTranslator;
use Core\Profile\Infrastructure\Persistence\Translators\TranslatorContract;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class EloquentModuleRepository implements ModuleRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;
    private ModuleModel $moduleModel;
    private ModuleTranslator $moduleTranslator;
    private TranslatorContract $modelModuleTranslator;
    private int $priority;
    
    public function __construct(
        ModuleModel $model,
        ModuleTranslator $moduleTranslator,
        TranslatorContract $modelModuleTranslator,
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->moduleModel = $model;
        $this->moduleTranslator = $moduleTranslator;
        $this->modelModuleTranslator =$modelModuleTranslator;
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
     * @throws ModuleNotFoundException
     * @throws Exception
     */
    public function find(ModuleId $id): null|Module
    {
        try {
            /** @var ModuleModel $moduleModel */
            $moduleModel = $this->moduleModel
                ->where('mod_id', $id->value())
                ->where('mod_state','>',ModuleState::STATE_DELETE)
                ->firstOrFail();
        } catch (Exception $exception) {
            throw new ModuleNotFoundException('Module not found with id: '. $id->value());
        }
        
        return $this->moduleTranslator->setModel($moduleModel)->toDomain();
    }

    public function persistModule(Module $module): Module
    {
        /** @var ModuleModel $moduleModel */
        $moduleModel = $this->modelModuleTranslator->executeTranslate($module);
        $moduleModel->save();

        return $module;
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Exception
     */
    public function getAll(array $filters = []): null|Modules
    {
        try {
            /** @var Builder $queryBuilder*/
            $queryBuilder = $this->moduleModel->where('mod_state','>',ModuleState::STATE_DELETE);
                
            if (array_key_exists('q', $filters) && isset($filters['q'])) {
                $queryBuilder->where('mod_search','like','%'.$filters['q'].'%');
            }
            
            /** @var Collection $moduleCollection*/
            $moduleCollection = $queryBuilder->get(['mod_id']);
        } catch (Exception $exception) {
            throw new ModulesNotFoundException('Modules not found');
        }
        
        $collection = [];
        /** @var ModuleModel $moduleModel */
        foreach ($moduleCollection as $moduleModel) {
            $collection[] = $moduleModel->id();
        }

        $modules = $this->moduleTranslator->setCollection($collection)->toDomainCollection();
        $modules->setFilters($filters);
        
        return $modules;
    }

    public function persistModules(Modules $modules): Modules
    {
        return $modules;
    }

    /**
     * @throws Throwable
     * @throws ModuleNotFoundException
     */
    public function deleteModule(ModuleId $id): void
    {
        /** @var ModuleModel $moduleModel */
        $moduleModel = $this->moduleModel
            ->where('mod_id', $id->value())
            ->where('mod_state','>',ModuleState::STATE_DELETE)
            ->first();

        if (is_null($moduleModel)){
            throw new ModuleNotFoundException('Module not found with id: '. $id->value());
        }

        try {
            $moduleModel->deleteOrFail();
        } catch (Exception $exception) {
            throw new ModuleDeleteException('Module can not be deleted with id: '. $id->value());
        }
    }
}