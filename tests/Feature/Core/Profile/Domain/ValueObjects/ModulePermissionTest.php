<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-02-19 22:30:10
 */

namespace Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModulePermission;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ModulePermission::class)]
class ModulePermissionTest extends TestCase
{
    private ModulePermission $valueObject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModulePermission();
    }

    protected function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnString(): void
    {
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('read', $result);
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $permission = 'edit';

        $result = $this->valueObject->setValue($permission);

        $this->assertSame($result, $this->valueObject);
        $this->assertInstanceOf(ModulePermission::class, $result);
    }
}
