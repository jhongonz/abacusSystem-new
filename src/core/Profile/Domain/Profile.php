<?php

namespace Core\Profile\Domain;

use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileSearch;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;

class Profile
{
    public const TYPE = 'profile';
    private ProfileId $id;
    private ProfileName $name;
    private ProfileState $state;
    private ProfileCreatedAt $createdAt;
    private ProfileUpdatedAt $updatedAt;
    private ProfileSearch $search;
    private ProfileDescription $description;
    private Modules $modules;

    private array $modulesAggregator = [];

    public function __construct(
      ProfileId $id,
      ProfileName $name,
      ProfileState $state = new ProfileState(),
      ProfileCreatedAt $createdAt = new ProfileCreatedAt()
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->state = $state;
        $this->createdAt = $createdAt;
        $this->search = new ProfileSearch();
        $this->updatedAt = new ProfileUpdatedAt();
        $this->modules = new Modules();
        $this->description = new ProfileDescription();
    }

    public function id(): ProfileId
    {
        return $this->id;
    }

    public function setId(ProfileId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function name(): ProfileName
    {
        return $this->name;
    }

    public function setName(ProfileName $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function state(): ProfileState
    {
        return $this->state;
    }

    public function search(): ProfileSearch
    {
        return $this->search;
    }

    public function setSearch(ProfileSearch $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function description(): ProfileDescription
    {
        return $this->description;
    }

    public function setDescription(ProfileDescription $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function modules(): Modules
    {
        return $this->modules;
    }

    public function setModules(Modules $modules): self
    {
        $this->modules = $modules;
        return $this;
    }

    public function modulesAggregator(): array
    {
        return $this->modulesAggregator;
    }

    public function setModulesAggregator(array $ids): self
    {
        $this->modulesAggregator = $ids;
        return $this;
    }

    public function setState(ProfileState $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function createdAt(): ProfileCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(ProfileCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updatedAt(): ProfileUpdatedAt
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(ProfileUpdatedAt $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function refreshSearch(): self
    {
        $dataSearch = [
            $this->name()->value(),
            $this->description()->value(),
        ];

        $this->search()->setValue(implode(' ', $dataSearch));
        return $this;
    }
}
