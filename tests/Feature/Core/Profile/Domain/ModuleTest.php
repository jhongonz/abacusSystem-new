<?php

namespace Tests\Feature\Core\Profile\Domain;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModulePosition;
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

    public function testIdShouldReturnValueObject(): void
    {
        $result = $this->module->id();
        $this->assertInstanceOf(ModuleId::class, $result);
        $this->assertSame($result, $this->moduleId);
    }

    /**
     * @throws Exception
     */
    public function testSetIdShouldChangeAndReturnSelf(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $result = $this->module->setId($moduleId);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($moduleId, $result->id());
    }

    public function testMenuKeyShouldReturnValueObject(): void
    {
        $result = $this->module->menuKey();
        $this->assertInstanceOf(ModuleMenuKey::class, $result);
        $this->assertSame($result, $this->moduleMenuKey);
    }

    /**
     * @throws Exception
     */
    public function testSetMenuKeyShouldChangeAndReturnSelf(): void
    {
        $menuKey = $this->createMock(ModuleMenuKey::class);
        $result = $this->module->setMenuKey($menuKey);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($menuKey, $result->menuKey());
    }

    public function testNameShouldReturnValueObject(): void
    {
        $result = $this->module->name();
        $this->assertInstanceOf(ModuleName::class, $result);
        $this->assertSame($result, $this->moduleName);
    }

    /**
     * @throws Exception
     */
    public function testSetNameShouldChangeAndReturnSelf(): void
    {
        $name = $this->createMock(ModuleName::class);
        $result = $this->module->setName($name);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($name, $result->name());
    }

    public function testRouteShouldReturnValueObject(): void
    {
        $result = $this->module->route();
        $this->assertInstanceOf(ModuleRoute::class, $result);
        $this->assertSame($result, $this->moduleRoute);
    }

    /**
     * @throws Exception
     */
    public function testSetRouteShouldChangeAndReturnSelf(): void
    {
        $route = $this->createMock(ModuleRoute::class);
        $result = $this->module->setRoute($route);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($route, $result->route());
    }

    public function testIconShouldReturnValueObject(): void
    {
        $result = $this->module->icon();
        $this->assertInstanceOf(ModuleIcon::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetIconShouldChangeAndReturnSelf(): void
    {
        $icon = $this->createMock(ModuleIcon::class);
        $result = $this->module->setIcon($icon);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($icon, $result->icon());
    }

    public function testStateShouldReturnValueObject(): void
    {
        $result = $this->module->state();
        $this->assertInstanceOf(ModuleState::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetStateShouldChangeAndReturnSelf(): void
    {
        $state = $this->createMock(ModuleState::class);
        $result = $this->module->setState($state);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($state, $result->state());
    }

    public function testCreatedAtShouldReturnValueObject(): void
    {
        $result = $this->module->createdAt();
        $this->assertInstanceOf(ModuleCreatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetCreatedAtShouldChangeAndReturnSelf(): void
    {
        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $result = $this->module->setCreatedAt($createdAt);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($createdAt, $result->createdAt());
    }

    public function testUpdatedAtShouldReturnValueObject(): void
    {
        $result = $this->module->updatedAt();
        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetUpdatedAtShouldChangeAndReturnSelf(): void
    {
        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $result = $this->module->setUpdatedAt($updatedAt);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($updatedAt, $result->updatedAt());
    }

    public function testSearchShouldReturnValueObject(): void
    {
        $result = $this->module->search();
        $this->assertInstanceOf(ModuleSearch::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetSearchShouldChangeAndReturnSelf(): void
    {
        $search = $this->createMock(ModuleSearch::class);
        $result = $this->module->setSearch($search);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($search, $result->search());
    }

    public function testOptionsShouldReturnValueObject(): void
    {
        $result = $this->module->options();
        $this->assertIsArray($result);
    }

    /**
     * @throws Exception
     */
    public function testSetOptionsShouldChangeAndReturnSelf(): void
    {
        $options = [$this->createMock(Module::class)];
        $result = $this->module->setOptions($options);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($options, $result->options());
    }

    public function testHaveChildrenShouldReturnBoolean(): void
    {
        $result = $this->module->haveChildren();
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    public function testExpandedShouldReturnBoolean(): void
    {
        $result = $this->module->expanded();
        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    public function testSetExpandedShouldReturnSelf(): void
    {
        $result = $this->module->setExpanded(true);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertTrue($result->expanded());
    }

    /**
     * @throws Exception
     */
    public function testRefreshSearchShouldReturnSelf(): void
    {
        $this->moduleMenuKey->expects(self::once())
            ->method('value')
            ->willReturn(' teSt ');

        $this->moduleName->expects(self::once())
            ->method('value')
            ->willReturn('  Test ');

        $this->moduleRoute->expects(self::once())
            ->method('value')
            ->willReturn('teST ');

        $this->moduleIcon->expects(self::once())
            ->method('value')
            ->willReturn(' tESt  ');

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

    public function testPositionShouldReturnValueObject(): void
    {
        $result = $this->module->position();
        $this->assertInstanceOf(ModulePosition::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testSetPositionShouldChangeAndReturnSelf(): void
    {
        $position = $this->createMock(ModulePosition::class);
        $result = $this->module->setPosition($position);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
        $this->assertSame($position, $result->position());
    }

    public function testIsParentShouldReturnBoolean(): void
    {
        $result = $this->module->isParent();

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }
}
