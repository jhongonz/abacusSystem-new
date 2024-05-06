<?php

namespace Tests\Feature\Core\Profile\Domain;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Module::class)]
class ModuleTest extends TestCase
{
    private ModuleId|MockObject $moduleId;
    private ModuleMenuKey|MockObject $moduleMenuKey;
    private ModuleName|MockObject $moduleName;
    private ModuleRoute|MockObject $moduleRoute;
    private ModuleIcon|MockObject $moduleIcon;
    private Module $module;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleId = $this->createMock(ModuleId::class);
        $this->moduleRoute = $this->createMock(ModuleRoute::class);
        $this->moduleName = $this->createMock(ModuleName::class);
        $this->moduleMenuKey = $this->createMock(ModuleMenuKey::class);
        $this->moduleIcon = $this->createMock(ModuleIcon::class);
        $this->module = new Module(
            $this->moduleId,
            $this->moduleMenuKey,
            $this->moduleName,
            $this->moduleRoute,
            $this->moduleIcon,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->module,
            $this->moduleId,
            $this->moduleName,
            $this->moduleRoute,
            $this->moduleMenuKey,
            $this->moduleIcon
        );
        parent::tearDown();
    }

    public function test_id_should_return_value_object(): void
    {
        $result = $this->module->id();
        $this->assertInstanceOf(ModuleId::class, $result);
        $this->assertSame($result, $this->moduleId);
    }

    /**
     * @throws Exception
     */
    public function test_setId_should_change_and_return_self(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $result = $this->module->setId($moduleId);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($moduleId, $result->id());
    }

    public function test_menuKey_should_return_value_object(): void
    {
        $result = $this->module->menuKey();
        $this->assertInstanceOf(ModuleMenuKey::class, $result);
        $this->assertSame($result, $this->moduleMenuKey);
    }

    /**
     * @throws Exception
     */
    public function test_setMenuKey_should_change_and_return_self(): void
    {
        $menuKey = $this->createMock(ModuleMenuKey::class);
        $result = $this->module->setMenuKey($menuKey);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($menuKey, $result->menuKey());
    }

    public function test_name_should_return_value_object(): void
    {
        $result = $this->module->name();
        $this->assertInstanceOf(ModuleName::class, $result);
        $this->assertSame($result, $this->moduleName);
    }

    /**
     * @throws Exception
     */
    public function test_setName_should_change_and_return_self(): void
    {
        $name = $this->createMock(ModuleName::class);
        $result = $this->module->setName($name);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($name, $result->name());
    }

    public function test_route_should_return_value_object(): void
    {
        $result = $this->module->route();
        $this->assertInstanceOf(ModuleRoute::class, $result);
        $this->assertSame($result, $this->moduleRoute);
    }

    /**
     * @throws Exception
     */
    public function test_setRoute_should_change_and_return_self(): void
    {
        $route = $this->createMock(ModuleRoute::class);
        $result = $this->module->setRoute($route);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($route, $result->route());
    }

    public function test_icon_should_return_value_object(): void
    {
        $result = $this->module->icon();
        $this->assertInstanceOf(ModuleIcon::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setIcon_should_change_and_return_self(): void
    {
        $icon = $this->createMock(ModuleIcon::class);
        $result = $this->module->setIcon($icon);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($icon, $result->icon());
    }

    public function test_state_should_return_value_object(): void
    {
        $result = $this->module->state();
        $this->assertInstanceOf(ModuleState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setState_should_change_and_return_self(): void
    {
        $state = $this->createMock(ModuleState::class);
        $result = $this->module->setState($state);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($state, $result->state());
    }

    public function test_createdAt_should_return_value_object(): void
    {
        $result = $this->module->createdAt();
        $this->assertInstanceOf(ModuleCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setCreatedAt_should_change_and_return_self(): void
    {
        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $result = $this->module->setCreatedAt($createdAt);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function test_updatedAt_should_return_value_object(): void
    {
        $result = $this->module->updatedAt();
        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setUpdatedAt_should_change_and_return_self(): void
    {
        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $result = $this->module->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($updatedAt, $result->updatedAt());
    }

    public function test_search_should_return_value_object(): void
    {
        $result = $this->module->search();
        $this->assertInstanceOf(ModuleSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setSearch_should_change_and_return_self(): void
    {
        $search = $this->createMock(ModuleSearch::class);
        $result = $this->module->setSearch($search);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($search, $result->search());
    }

    public function test_options_should_return_value_object(): void
    {
        $result = $this->module->options();
        $this->assertIsArray($result);
    }

    public function test_setOptions_should_change_and_return_self(): void
    {
        $options = [1,2,3];
        $result = $this->module->setOptions($options);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($options, $result->options());
    }

    public function test_haveChildren_should_return_boolean(): void
    {
        $result = $this->module->haveChildren();
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    public function test_expanded_should_return_boolean(): void
    {
        $result = $this->module->expanded();
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    public function test_setExpanded_should_return_self(): void
    {
        $result = $this->module->setExpanded(true);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertTrue($result->expanded());
    }

    /**
     * @throws Exception
     */
    public function test_refreshSearch_should_return_self(): void
    {
        $this->moduleMenuKey->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $this->moduleName->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $this->moduleRoute->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $this->moduleIcon->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $searchMock = $this->createMock(ModuleSearch::class);
        $searchMock->expects(self::once())
            ->method('setValue')
            ->with('test test test test')
            ->willReturnSelf();

        $this->module->setSearch($searchMock);
        $result = $this->module->refreshSearch();

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
    }
}
