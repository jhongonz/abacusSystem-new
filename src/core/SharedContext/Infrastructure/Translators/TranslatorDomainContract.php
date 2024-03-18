<?php

namespace Core\SharedContext\Infrastructure\Translators;

interface TranslatorDomainContract
{
    public function setModel(mixed $model): TranslatorDomainContract;
    
    public function toDomain(): mixed;
    
    public function canTranslate(): string;
}