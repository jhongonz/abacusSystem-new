<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 13:33:10
 */

namespace Core\Campus\Application\DataTransformer;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;

class CampusDataTransformer implements CampusDataTransformerContract
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private Campus $campus;

    public function write(Campus $campus): self
    {
        $this->campus = $campus;
        return $this;
    }

    public function read(): array
    {
        return [
          Campus::TYPE => $this->retrieveData()
        ];
    }

    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->campus->state()->formatHtmlToState();

        return $data;
    }

    private function retrieveData(): array
    {
        $data = [
            'id' => $this->campus->id()->value(),
            'institutionId' => $this->campus->institutionId()->value(),
            'name' => $this->campus->name()->value(),
            'address' => $this->campus->address()->value(),
            'phone' => $this->campus->phone()->value(),
            'email' => $this->campus->email()->value(),
            'observations' => $this->campus->observations()->value(),
            'search' => $this->campus->search()->value(),
            'state' => $this->campus->state()->value(),
            'createdAt' => $this->campus->createdAt()->value()->format(self::DATE_FORMAT),
        ];

        $updatedAt = $this->campus->updatedAt()->value();
        $data['updatedAt'] = (! is_null($updatedAt)) ? $updatedAt->format(self::DATE_FORMAT) : null;

        return $data;
    }
}
