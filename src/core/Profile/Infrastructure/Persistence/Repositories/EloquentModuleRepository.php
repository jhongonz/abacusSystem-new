<?php

namespace Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Exceptions\ModuleNotFoundException;
use Core\Profile\Exceptions\ModulesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module as ModuleModel;
use Core\Profile\Infrastructure\Persistence\Translators\ModuleTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

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
        ModuleModel $model,
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->database = $database;
        $this->moduleTranslator = $moduleTranslator;
        $this->model = $model;
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
    public function find(ModuleId $id): Module
    {
        $builder = $this->database->table($this->getTable())
            ->where('mod_id', $id->value())
            ->where('mod_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();

        if (is_null($data)) {
            throw new ModuleNotFoundException('Module not found with id: '.$id->value());
        }

        $moduleModel = $this->updateAttributesModelModule((array) $data);

        return $this->moduleTranslator->setModel($moduleModel)->toDomain();
    }

    public function persistModule(Module $module): Module
    {
        $moduleModel = $this->domainToModel($module);
        $moduleId = $moduleModel->id();
        $dataModel = $moduleModel->toArray();

        $builder = $this->database->table($this->getTable());

        if (is_null($moduleId)) {
            $moduleId = $builder->insertGetId($dataModel);
            $module->id()->setValue($moduleId);
        } else {
            $builder->where('mod_id', $moduleId);
            $builder->update($dataModel);
        }

        return $module;
    }

    /**
     * @throws ModulesNotFoundException
     * @throws Exception
     */
    public function getAll(array $filters = []): ?Modules
    {
        /** @var Builder $builder */
        $builder = $this->database->table($this->getTable())
            ->where('mod_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }

        $builder->orderBy('mod_position');
        $moduleCollection = $builder->get(['mod_id']);

        if (empty($moduleCollection)) {
            throw new ModulesNotFoundException('Modules not found');
        }

        $collection = [];
        foreach ($moduleCollection as $item) {
            $moduleModel = $this->updateAttributesModelModule((array) $item);
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
     * @throws ModuleNotFoundException
     */
    public function deleteModule(ModuleId $id): void
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('mod_id', $id->value());
        $data = $builder->first();

        if (is_null($data)) {
            throw new ModuleNotFoundException('Module not found with id: '.$id->value());
        }

        $moduleModel = $this->updateAttributesModelModule((array) $data);
        $moduleModel->changeState(ValueObjectStatus::STATE_DELETE);
        $moduleModel->changeDeletedAt(new \DateTime);
        $dataModel = $moduleModel->toArray();

        $builder->update($dataModel);
    }

    private function domainToModel(Module $domain): ModuleModel
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('mod_id', $domain->id()->value());
        $data = $builder->first();
        $model = $this->updateAttributesModelModule((array) $data);

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

    private function updateAttributesModelModule(array $data = []): ModuleModel
    {
        $this->model->fill($data);

        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }
}
