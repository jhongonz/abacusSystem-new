<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Translators;

interface TranslatorContract
{
    public function executeTranslate(mixed $domain, mixed $model = null): mixed;

    public function canTranslate(): string;
    public function canTranslateTo(): string;
}
