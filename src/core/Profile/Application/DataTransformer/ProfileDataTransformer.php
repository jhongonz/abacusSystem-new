<?php

namespace Core\Profile\Application\DataTransformer;

use Core\Profile\Domain\Contracts\ProfileDataTransformerContract;
use Core\Profile\Domain\Profile;
use Exception;

class ProfileDataTransformer implements ProfileDataTransformerContract
{
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
            'createdAt' => $this->profile->createdAt()->toFormattedString(),
        ];

        $updatedAt = $this->profile->updatedAt()->toFormattedString();
        $data['updatedAt'] = (!empty($updatedAt)) ? $updatedAt : null;
        $data['modulesAggregator'] = $this->profile->modulesAggregator();

        return $data;
    }
}
