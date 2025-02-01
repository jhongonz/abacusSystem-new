<?php

namespace Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class SearchProfileByIdRequest implements RequestService
{
    public function __construct(
        private readonly ProfileId $id,
    ) {
    }

    public function profileId(): ProfileId
    {
        return $this->id;
    }
}
