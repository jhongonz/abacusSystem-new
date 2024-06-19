<?php

namespace Core\Profile\Application\DataTransformer;

use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Module;
use Exception;

class ModuleDataTransformer implements ModuleDataTransformerContract
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private Module $module;

    public function write(Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function read(): array
    {
        return [
            Module::TYPE => $this->retrieveData(),
        ];
    }

    /**
     * @throws Exception
     */
    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->module->state()->formatHtmlToState();

        return $data;
    }

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
            'createdAt' => $this->module->createdAt()->value()->format(self::DATE_FORMAT),
        ];

        $updatedAt = $this->module->updatedAt()->value();
        $data['updatedAt'] = (! is_null($updatedAt)) ? $updatedAt->format(self::DATE_FORMAT) : null;

        return $data;
    }
}
