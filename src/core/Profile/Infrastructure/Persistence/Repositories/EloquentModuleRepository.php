<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleDeleteException;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module as ModuleModel;
use Core\Profile\Infrastructure\Persistence\Translators\ModuleTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class EloquentModuleRepository implements ChainPriority, ModuleRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    private ModuleModel $model;

    private ModuleTranslator $moduleTranslator;

    private DatabaseManager $database;

    private int $priority;

    public function __construct(
        DatabaseManager $database,
        ModuleTranslator $moduleTranslator,
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->database = $database;
        $this->moduleTranslator = $moduleTranslator;
        $this->priority = $priority;

        $this->model = $this->createModel();
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
    public function find(ModuleId $id): ?Module
    {
        $data = $this->database->table($this->model->getTable())
            ->where('mod_id', $id->value())
            ->where('mod_state', '>', ValueObjectStatus::STATE_DELETE)
            ->first();

        if (is_null($data)) {
            throw new ModuleNotFoundException('Module not found with id: '.$id->value());
        }

        $moduleModel = $this->createModel((array) $data);

        return $this->moduleTranslator->setModel($moduleModel)->toDomain();
    }

    public function persistModule(Module $module): Module
    {
        $moduleModel = $this->domainToModel($module);
        $moduleModel->save();
        $module->id()->setValue($moduleModel->id());

        return $module;
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Exception
     */
    public function getAll(array $filters = []): ?Modules
    {
        try {
            /** @var Builder $queryBuilder */
            $queryBuilder = $this->database->table($this->model->getTable())
                ->where('mod_state', '>', ValueObjectStatus::STATE_DELETE);

            if (array_key_exists('q', $filters) && isset($filters['q'])) {
                $queryBuilder->where('mod_search', 'like', '%'.$filters['q'].'%');
            }

            /** @var Collection $moduleCollection */
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
        $data = $this->database->table($this->model->getTable())
            ->find($id->value());

        if (is_null($data)) {
            throw new ModuleNotFoundException('Module not found with id: '.$id->value());
        }

        $moduleModel = $this->createModel($data);
        try {
            $moduleModel->deleteOrFail();
        } catch (Exception $exception) {
            throw new ModuleDeleteException('Module can not be deleted with id: '.$id->value(), $exception->getTrace());
        }
    }

    protected function domainToModel(Module $domain, ?ModuleModel $model = null): ModuleModel
    {
        if (is_null($model)) {
            $data = $this->database->table($this->model->getTable())->find($domain->id()->value());
            $model = $this->createModel($data);
        }

        $model->changeId($domain->id()->value());
        $model->changeName($domain->name()->value());
        $model->changeMenuKey($domain->menuKey()->value());
        $model->changeRoute($domain->route()->value());
        $model->changeIcon($domain->icon()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (! is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    protected function createModel(array $data = []): ModuleModel
    {
        return new ModuleModel($data);
    }
}
