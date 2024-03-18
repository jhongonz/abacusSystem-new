<?php

namespace Core\SharedContext\Infrastructure\Persistence;

interface ChainPriority
{
    public function priority(): int;
    
    public function changePriority(int $priority);
}