<?php

namespace Core\Profile\Infrastructure\Persistence\Translators;

interface TranslatorContract
{
    public function executeTranslate(mixed $domain, mixed $model = null): mixed;

    public function canTranslate(): string;
    public function canTranslateTo(): string;
}
