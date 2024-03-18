<?php

namespace Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class SearchProfileByIdRequest implements RequestService
{
    private ProfileId $id;
    
    public function __construct(ProfileId $id)
    {
        $this->id = $id;
    }
    
    public function profileId(): ProfileId
    {
        return $this->id;
    }
}