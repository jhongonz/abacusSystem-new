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
        $this->model = new Module();
    }

    public function tearDown(): void
    {
        unset($this->model, $this->modelMock);
        parent::tearDown();
    }

    public function testGetSearchFieldShouldReturnString(): void
    {
        $result = $this->model->getSearchField();

        $this->assertIsString($result);
        $this->assertSame('mod_search', $result);
    }

    /**
     * @throws Exception
     */
    public function testProfilesShouldReturnRelation(): void
    {
        $relationBelongsToManyMock = $this->createMock(BelongsToMany::class);
        $relationBelongsToManyMock->expects(self::once())
            ->method('withPivot')
            ->with(
                'pri__pro_id',
                'pri__mod_id',
                'pri_permission',
                'created_at',
                'updated_at',
                'deleted_at'
            )
            ->willReturnSelf();

        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['newRelatedInstance', 'belongsToMany', 'newBelongsToMany'])
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

    public function testIdShouldReturnInt(): void
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

    public function testIdShouldReturnNull(): void
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

    public function testChangeIdShouldReturnSelf(): void
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

    public function testMenuKeyShouldReturnString(): void
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

    public function testMenuKeyShouldReturnNull(): void
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

    public function testChangeMenuKeyShouldReturnSelf(): void
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

    public function testNameShouldReturnString(): void
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

    public function testChangeNameShouldReturnSelf(): void
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

    public function testRouteShouldReturnString(): void
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

    public function testRouteShouldReturnNull(): void
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

    public function testChangeRouteShouldReturnSelf(): void
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

    public function testIconShouldReturnString(): void
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

    public function testIconShouldReturnNull(): void
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

    public function testChangeIconShouldReturnSelf(): void
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

    public function testSearchShouldReturnString(): void
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

    public function testSearchShouldReturnNull(): void
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

    public function testChangeSearchShouldReturnSelf(): void
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

    public function testStateShouldReturnInt(): void
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

    public function testChangeStateShouldReturnSelf(): void
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
    public function testCreatedAtShouldReturnDatetime(): void
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

    public function testChangeCreatedAtShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime();
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
    public function testUpdatedAtShouldReturnDatetime(): void
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
    public function testUpdatedAtShouldReturnNull(): void
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

    public function testChangeUpdatedAtShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime();
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
    public function testDeletedAtShouldReturnNull(): void
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

    public function testChangeDeletedAtShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime();
        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('deleted_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeDeletedAt($datetime);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    /**
     * @throws \Exception
     */
    public function testPositionShouldReturnInt(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('mod_position')
            ->willReturn(1);

        $result = $this->modelMock->position();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testChangePositionShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(Module::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('mod_position', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changePosition(2);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCastsShouldReturnArray(): void
    {
        $this->modelMock = new Module();

        $reflection = new \ReflectionClass(Module::class);
        $method = $reflection->getMethod('casts');
        $this->assertTrue($method->isProtected());

        $result = $method->invoke($this->modelMock);

        $dataExpected = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
        $this->assertIsArray($result);
        $this->assertSame($dataExpected, $result);
    }
}
