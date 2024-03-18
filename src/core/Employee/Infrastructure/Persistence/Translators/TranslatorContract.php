<?php

namespace Core\Employee\Infrastructure\Persistence\Translators;

interface TranslatorContract
{
    public function executeTranslate(mixed $domain, mixed $model): mixed;

    public function canTranslate(): string;
    
    public function canTranslateTo(): string;
}