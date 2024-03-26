<?php

namespace Core\Profile\Domain;

use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;

class Module
{
    public const TYPE = 'module';
    private ModuleId $id;
    private ModuleMenuKey $menuKey;
    private ModuleName $name;
    private ModuleRoute $route;
    private ModuleIcon $icon;
    private ModuleState $state;
    private ModuleCreatedAt $createdAt;
    private ModuleUpdatedAt $updatedAt;
    private ModuleSearch $search;
    private array $options = [];
    private bool $expanded = false;

    public function __construct(
        ModuleId $id,
        ModuleMenuKey $menuKey,
        ModuleName $name,
        ModuleRoute $route,
        ModuleIcon $icon = new ModuleIcon(),
        ModuleState $state = new ModuleState(),
        ModuleCreatedAt $createdAt = new ModuleCreatedAt(),
    ) {
        $this->id = $id;
        $this->menuKey = $menuKey;
        $this->name = $name;
        $this->route = $route;
        $this->icon = $icon;
        $this->state = $state;
        $this->createdAt = $createdAt;

        $this->search = new ModuleSearch();
        $this->updatedAt = new ModuleUpdatedAt();
        //$this->options = [];
        //$this->expanded = false;
    }

    public function id(): ModuleId
    {
        return $this->id;
    }

    public function setId(ModuleId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function menuKey(): ModuleMenuKey
    {
        return $this->menuKey;
    }

    public function setMenuKey(ModuleMenuKey $menuKey): self
    {
        $this->menuKey = $menuKey;
        return $this;
    }

    public function name(): ModuleName
    {
        return $this->name;
    }

    public function setName(ModuleName $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function route(): ModuleRoute
    {
        return $this->route;
    }

    public function setRoute(ModuleRoute $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function icon():ModuleIcon
    {
        return $this->icon;
    }

    public function setIcon(ModuleIcon $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function state(): ModuleState
    {
        return $this->state;
    }

    public function setState(ModuleState $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function createdAt(): ModuleCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(ModuleCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updatedAt(): ModuleUpdatedAt
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(ModuleUpdatedAt $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function search(): ModuleSearch
    {
        return $this->search;
    }

    public function setSearch(ModuleSearch $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function options(): array
    {
        return $this->options;
    }

    public function setOptions(array $data): self
    {
        $this->options = $data;
        return $this;
    }

    public function haveChildren(): bool
    {
        return (count($this->options()) > 0);
    }

    public function expanded(): bool
    {
        return $this->expanded;
    }

    public function setExpanded(bool $value): self
    {
        $this->expanded = $value;
        return $this;
    }

    public function refreshSearch(): self
    {
        $dataSearch = [
            $this->menuKey()->value(),
            $this->name()->value(),
            $this->route()->value(),
            $this->icon()->value(),
        ];

        $this->search()->setValue(implode(' ', $dataSearch));
        return $this;
    }
}
