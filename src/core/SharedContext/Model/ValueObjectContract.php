<?php

namespace Core\SharedContext\Model;

interface ValueObjectContract
{
    public function value(): mixed;

    public function setValue(mixed $value): self;
}
