<?php

namespace Tests\Feature\Core\Profile\Domain;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Modules::class)]
class ModulesTest extends TestCase
{
    private Module|MockObject $module;

    private Modules $modules;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->module = $this->createMock(Module::class);
        $this->modules = new Modules($this->module);
    }

    public function tearDown(): void
    {
        unset(
            $this->modules,
            $this->module
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_addItem_should_return_self(): void
    {
        $moduleMock = $this->createMock(Module::class);
        $result = $this->modules->addItem($moduleMock);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($result, $this->modules);
    }

    public function test_items_should_return_array(): void
    {
        $result = $this->modules->items();
        $this->assertIsArray($result);
    }

    public function test_filters_should_return_array(): void
    {
        $result = $this->modules->filters();
        $this->assertIsArray($result);
    }

    public function test_setFilters_should_return_self(): void
    {
        $filters = ['hello'];
        $result = $this->modules->setFilters($filters);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($result, $this->modules);
        $this->assertSame($filters, $result->filters());
    }

    /**
     * @throws Exception
     */
    public function test_moduleElementsOfKey_should_return_array(): void
    {
        $menuKeyMock = $this->createMock(ModuleMenuKey::class);
        $menuKeyMock->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $this->module->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKeyMock);

        $result = $this->modules->moduleElementsOfKey('test');

        $this->assertIsArray($result);
        $this->assertSame([$this->module], $result);
    }

    public function test_addId_should_return_self(): void
    {
        $result = $this->modules->addId(1);
        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($result, $this->modules);
        $this->assertSame([1], $result->aggregator());
    }

    public function test_aggregator_should_return_array(): void
    {
        $result = $this->modules->aggregator();
        $this->assertIsArray($result);
    }
}
