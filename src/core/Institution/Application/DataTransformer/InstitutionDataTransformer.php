<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 07:47:14
 */

namespace Core\Institution\Application\DataTransformer;

use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Institution;

class InstitutionDataTransformer implements InstitutionDataTransformerContract
{
    private Institution $institution;

    public function write(Institution $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function read(): array
    {
        return [
            Institution::TYPE => $this->retrieveData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->institution->state()->formatHtmlToState();

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function retrieveData(): array
    {
        $data = [
            'id' => $this->institution->id()->value(),
            'code' => $this->institution->code()->value(),
            'name' => $this->institution->name()->value(),
            'shortname' => $this->institution->shortname()->value(),
            'logo' => $this->institution->logo()->value(),
            'observations' => $this->institution->observations()->value(),
            'address' => $this->institution->address()->value(),
            'phone' => $this->institution->phone()->value(),
            'email' => $this->institution->email()->value(),
            'state' => $this->institution->state()->value(),
            'search' => $this->institution->search()->value(),
            'createdAt' => $this->institution->createdAt()->toFormattedString(),
        ];

        $updatedAt = $this->institution->updatedAt()->toFormattedString();
        $data['updatedAt'] = !empty($updatedAt) ? $updatedAt : null;

        return $data;
    }
}
