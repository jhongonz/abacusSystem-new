<?php

namespace Tests\Feature\Core\Profile\Domain;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\SharedContext\Model\ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Modules::class)]
#[CoversClass(ArrayIterator::class)]
class ModulesTest extends TestCase
{
    private Module|MockObject $module;

    private Modules|MockObject $modules;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->module = $this->createMock(Module::class);
        $this->modules = new Modules([$this->module]);
    }

    public function tearDown(): void
    {
        unset(
            $this->modules,
            $this->module
        );
        parent::tearDown();
    }

    public function testConstructShouldReturnExceptionWhenItemIsNotValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item is not valid to collection Core\Profile\Domain\Modules');

        $this->modules = new Modules(['testing']);
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldReturnSelf(): void
    {
        $moduleMock = $this->createMock(Module::class);
        $result = $this->modules->addItem($moduleMock);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($result, $this->modules);
        $this->assertCount(2, $result->items());
        $this->assertSame([$this->module, $moduleMock], $result->items());
    }

    public function testItemsShouldReturnArray(): void
    {
        $result = $this->modules->items();
        $this->assertIsArray($result);
    }

    public function testFiltersShouldReturnArray(): void
    {
        $result = $this->modules->filters();
        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldReturnSelf(): void
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
    public function testModuleElementsOfKeyShouldReturnArray(): void
    {
        $keyExpected = 'test';

        $menuKeyMock = $this->createMock(ModuleMenuKey::class);
        $menuKeyMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn($keyExpected);

        $this->module->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKeyMock);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKeyMock);

        $moduleCollectionMock = [$moduleMock, $this->module];
        $this->modules = $this->getMockBuilder(Modules::class)
            ->setConstructorArgs([
                $moduleCollectionMock,
            ])
            ->onlyMethods(['items'])
            ->getMock();

        $this->modules->expects(self::once())
            ->method('items')
            ->willReturn($moduleCollectionMock);

        $result = $this->modules->moduleElementsOfKey($keyExpected);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame([$moduleMock, $this->module], $result);
    }

    public function testAddIdShouldReturnSelf(): void
    {
        $result = $this->modules->addId(1);
        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($result, $this->modules);
        $this->assertSame([1], $result->aggregator());
    }

    public function testAggregatorShouldReturnArray(): void
    {
        $result = $this->modules->aggregator();
        $this->assertIsArray($result);
    }
}
