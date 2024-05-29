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

    public function read(): array
    {
        return [
            Institution::TYPE => $this->retrieveData()
        ];
    }

    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->institution->state()->formatHtmlToState();

        return $data;
    }

    private function retrieveData(): array
    {
        return [
            'id' => $this->institution->id()->value(),
            'code' => $this->institution->code()->value(),
            'name' => $this->institution->name()->value(),
            'shortname' => $this->institution->shortname()->value(),
            'logo' => $this->institution->logo()->value(),
            'observations' => $this->institution->observations()->value(),
            'state' => $this->institution->state()->value(),
            'search' => $this->institution->search()->value(),
            'createdAt' => $this->institution->createdAt()->value(),
            'updatedAt' => $this->institution->updatedAt()->value(),
        ];
    }
}
