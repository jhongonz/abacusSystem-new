<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

interface TranslatorContract
{
    public function executeTranslate(mixed $source, mixed $destiny = null): mixed;
    
    public function canTranslate(): string;
    public function canTranslateTo(): string;
}