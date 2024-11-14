<?php

namespace Core\Profile\Application\DataTransformer;

use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Module;

class ModuleDataTransformer implements ModuleDataTransformerContract
{
    private Module $module;

    public function write(Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function read(): array
    {
        return [
            Module::TYPE => $this->retrieveData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->module->state()->formatHtmlToState();

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function retrieveData(): array
    {
        $data = [
            'id' => $this->module->id()->value(),
            'key' => $this->module->menuKey()->value(),
            'name' => $this->module->name()->value(),
            'route' => $this->module->route()->value(),
            'icon' => $this->module->icon()->value(),
            'state' => $this->module->state()->value(),
            'position' => $this->module->position()->value(),
            'createdAt' => $this->module->createdAt()->toFormattedString(),
        ];

        $updatedAt = $this->module->updatedAt()->toFormattedString();
        $data['updatedAt'] = (!empty($updatedAt)) ? $updatedAt : null;

        return $data;
    }
}
