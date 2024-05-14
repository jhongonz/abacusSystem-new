<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Profile::class)]
class ProfileTest extends TestCase
{
    private Profile $model;
    private Profile|MockObject $modelMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Profile;
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
        $this->assertSame('pro_search', $result);
    }

    /**
     * @throws Exception
     */
    public function test_user_should_return_relation(): void
    {
        $relationHasMany = $this->createMock(HasMany::class);
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['newRelatedInstance','hasMany','newHasMany'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('hasMany')
            ->with(User::class, 'user__pro_id', 'pro_id')
            ->willReturn($relationHasMany);

        $result = $this->modelMock->user();

        $this->assertInstanceOf(HasMany::class, $result);
        $this->assertSame($relationHasMany, $result);
    }

    /**
     * @throws Exception
     */
    public function test_pivotModules_should_return_relation(): void
    {
        $relationMock = $this->createMock(BelongsToMany::class);
        $relationMock->expects(self::once())
            ->method('withPivot')
            ->with(
                'pri__pro_id',
                'pri__mod_id',
                'created_at',
                'updated_at',
                'deleted_at'
            )
            ->willReturnSelf();

        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['newRelatedInstance','belongsToMany','newBelongsToMany'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('belongsToMany')
            ->with(
                Module::class,
                'privileges',
                'pri__pro_id',
                'pri__mod_id',
            )
            ->willReturn($relationMock);

        $result = $this->modelMock->pivotModules();

        $this->assertInstanceOf(BelongsToMany::class, $result);
        $this->assertSame($relationMock, $result);
    }

    public function test_modules_should_return_model(): void
    {
        $result = $this->model->modules();
        $this->assertInstanceOf(Module::class, $result);
    }

    public function test_id_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_id')
            ->willReturn(1);

        $result = $this->modelMock->id();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_id_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_id')
            ->willReturn(null);

        $result = $this->modelMock->id();

        $this->assertNull($result);
    }

    public function test_changeId_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('pro_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(2);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_name_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_name')
            ->willReturn('test');

        $result = $this->modelMock->name();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_name_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_name')
            ->willReturn(null);

        $result = $this->modelMock->name();

        $this->assertNull($result);
    }

    public function test_changeName_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('pro_name', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeName('testing');

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_state_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_state')
            ->willReturn(1);

        $result = $this->modelMock->state();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_changeState_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('pro_state', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeState(2);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_search_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_search')
            ->willReturn('test');

        $result = $this->modelMock->search();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_search_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_search')
            ->willReturn(null);

        $result = $this->modelMock->search();

        $this->assertNull($result);
    }

    public function test_changeSearch_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('pro_search', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeSearch('testing');

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    public function test_description_should_return_string(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_description')
            ->willReturn('test');

        $result = $this->modelMock->description();

        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function test_description_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('pro_description')
            ->willReturn(null);

        $result = $this->modelMock->description();

        $this->assertNull($result);
    }

    public function test_changeDescription_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('pro_description', 'testing')
            ->willReturnSelf();

        $result = $this->modelMock->changeDescription('testing');

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_createdAt_should_return_datetime(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn('2024-05-12 22:07:00');

        $result = $this->modelMock->createdAt();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function test_changeCreatedAt_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime('2024-05-12 22:07:00');
        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('created_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeCreatedAt($datetime);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }

    /**
     * @throws \Exception
     */
    public function test_updatedAt_should_return_datetime(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn('2024-05-12 22:07:00');

        $result = $this->modelMock->updatedAt();

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function test_changeUpdatedAt_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(Profile::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $datetime = new \DateTime('2024-05-12 22:07:00');
        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('updated_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeUpdatedAt($datetime);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($this->modelMock, $result);
    }
}
