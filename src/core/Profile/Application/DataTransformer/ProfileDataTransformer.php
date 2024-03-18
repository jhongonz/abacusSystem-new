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
            Profile::TYPE => $this->retrieveData()
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
        return [
            'id' => $this->profile->id()->value(),
            'name' => $this->profile->name()->value(),
            'state' => $this->profile->state()->value(),
            'createdAt' => $this->profile->createdAt()->value(),
            'updatedAt' => $this->profile->updatedAt()->value(),
            'modulesAggregator' => $this->profile->modulesAggregator()
        ];
    }
}