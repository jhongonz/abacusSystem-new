<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Repositories\Translators;

use Core\User\Infrastructure\Persistence\Translators\DomainToModelUserTranslator;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DomainToModelUserTranslator::class)]
class DomainToModelUserTranslatorTest extends TestCase
{
}
