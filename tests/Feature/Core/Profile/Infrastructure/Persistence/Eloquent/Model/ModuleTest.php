<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Module::class)]
class ModuleTest extends TestCase
{
    private Module $model;
    private Module|MockObject $modelMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Module;
    }

    public function tearDown(): void
    {
        unset($this->model, $this->modelMock);
        parent::tearDown();
    }

    public function test_getSearchField_should_return_string(): void
    {
        $result = $this->model->getSearchField();

        $this->assertIsString($result);
        $this->assertSame('mod_search', $result);
    }

    /**
     * @throws Exception
     */
    public function test_profiles_should_return_relation(): void
    {
        $relationBelongsToManyMock = $this->createMock(BelongsToMany::class);
        $relationBelongsToManyMock->expects(self::once())
            ->method('withPivot')
            ->with(
                'pri__pro_id',
                'pri__mod_id',
                'created_at',
                'updated_at',
                'deleted_at'
            )
            ->willReturnSelf();

        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['newRelatedInstance','belongsToMany','newBelongsToMany'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('belongsToMany')
            ->with(
                Profile::class,
                'privileges',
                'pri__mod_id',
                'pri__pro_id'
            )
            ->willReturn($relationBelongsToManyMock);

        $result = $this->modelMock->profiles();

        $this->assertInstanceOf(BelongsToMany::class, $result);
        $this->assertSame($relationBelongsToManyMock, $result);
    }

    public function test_id_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_id')
            ->willReturn(1);

        $result = $this->modelMock->id();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_id_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_id')
            ->willReturn(null);

        $result = $this->modelMock->id();

        $this->assertNull($result);
    }

    public function test_changeId_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_id', 1)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(1);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_menuKey_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_menu_key')
            ->willReturn('test');

        $result = $this->modelMock->menuKey();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_menuKey_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_menu_key')
            ->willReturn(null);

        $result = $this->modelMock->menuKey();

        $this->assertNull($result);
    }

    public function test_changeMenuKey_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_menu_key', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeMenuKey('testing');

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_name_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_name')
            ->willReturn('test');

        $result = $this->modelMock->name();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_changeName_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_name', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeName('testing');

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_route_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_route')
            ->willReturn('test');

        $result = $this->modelMock->route();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_route_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_route')
            ->willReturn(null);

        $result = $this->modelMock->route();

        $this->assertNull($result);
    }

    public function test_changeRoute_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_route', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeRoute('testing');

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_icon_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_icon')
            ->willReturn('test');

        $result = $this->modelMock->icon();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_icon_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_icon')
            ->willReturn(null);

        $result = $this->modelMock->icon();

        $this->assertNull($result);
    }

    public function test_changeIcon_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_icon', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeIcon('testing');

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_search_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_search')
            ->willReturn('test');

        $result = $this->modelMock->search();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_search_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_search')
            ->willReturn(null);

        $result = $this->modelMock->search();

        $this->assertNull($result);
    }

    public function test_changeSearch_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_search', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeSearch('testing');

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_state_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_state')
            ->willReturn(2);

        $result = $this->modelMock->state();

        $this->assertIsInt($result);
        $this->assertSame(2, $result);
    }

    public function test_changeState_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_state', 1)
            ->willReturnSelf();

        $result = $this->modelMock->changeState(1);

        $this->assertInstanceOf(Module::class, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_createdAt_should_return_datetime(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn('2024-05-12 15:58:00');

        $result = $this->modelMock->createdAt();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function test_changeCreatedAt_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime;
        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('created_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeCreatedAt($datetime);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_updatedAt_should_return_datetime(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn('2024-05-12 15:58:00');

        $result = $this->modelMock->updatedAt();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_updatedAt_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn(null);

        $result = $this->modelMock->updatedAt();

        $this->assertNull($result);
    }

    public function test_changeUpdatedAt_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime;
        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('updated_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeUpdatedAt($datetime);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_deletedAt_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('deleted_at')
            ->willReturn(null);

        $result = $this->modelMock->deletedAt();

        $this->assertNull($result);
    }

    public function test_changeDeletedAt_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime;
        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('deleted_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeDeletedAt($datetime);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }
}
