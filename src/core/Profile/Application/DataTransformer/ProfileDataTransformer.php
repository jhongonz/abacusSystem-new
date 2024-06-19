<?php

namespace Core\Profile\Application\DataTransformer;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Profile;
use Exception;

class ProfileDataTransformer implements ProfileDataTransformerContract
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private Profile $profile;

    public function write(Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function read(): array
    {
        return [
            Profile::TYPE => $this->retrieveData(),
        ];
    }

    /**
     * @throws Exception
     */
    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->profile->state()->formatHtmlToState();

        return $data;
    }

    private function retrieveData(): array
    {
        $data = [
            'id' => $this->profile->id()->value(),
            'name' => $this->profile->name()->value(),
            'description' => $this->profile->description()->value(),
            'state' => $this->profile->state()->value(),
            'modulesAggregator' => $this->profile->modulesAggregator(),
            'createdAt' => $this->profile->createdAt()->value()->format(self::DATE_FORMAT),
        ];

        $updatedAt = $this->profile->updatedAt()->value();
        $data['updatedAt'] = (! is_null($updatedAt)) ? $updatedAt->format(self::DATE_FORMAT) : null;

        return $data;
    }
}
