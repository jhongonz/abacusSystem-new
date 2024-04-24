<?php

namespace Tests\Feature\Core\User\Infrastructure\Persistence\Eloquent\Model;

use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    private User $model;
    private User|MockObject $modelMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new User();
    }

    public function tearDown(): void
    {
        unset($this->model, $this->modelMock);
        parent::tearDown();
    }

    public function test_getSearchField_should_return_string(): void
    {
        $expected = 'user_login';
        $result = $this->model->getSearchField();

        $this->assertSame($expected, $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function test_relationWithEmployee_should_return_belongsTo(): void
    {
        $relationMock = $this->createMock(BelongsTo::class);

        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['belongsTo','newBelongsTo','newRelatedInstance','guessBelongsToRelation'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('belongsTo')
            ->with(Employee::class,'user__emp_id','emp_id')
            ->willReturn($relationMock);

        $result = $this->modelMock->relationWithEmployee();

        $this->assertInstanceOf(BelongsTo::class, $result);
        $this->assertSame($relationMock, $result);
    }

    public function test_employee_should_return_employee_model(): void
    {
        $result = $this->model->employee();
        $this->assertInstanceOf(Employee::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_relationWithProfile_should_return_belongsTo(): void
    {
        $relationMock = $this->createMock(BelongsTo::class);

        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['belongsTo','newBelongsTo','newRelatedInstance','guessBelongsToRelation'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('belongsTo')
            ->with(Profile::class,'user__pro_id','pro_id')
            ->willReturn($relationMock);

        $result = $this->modelMock->relationWithProfile();

        $this->assertInstanceOf(BelongsTo::class, $result);
        $this->assertSame($relationMock, $result);
    }

    public function test_profile_should_return_profile_model(): void
    {
        $result = $this->model->profile();
        $this->assertInstanceOf(Profile::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_id_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_id')
            ->willReturn(1);

        $result = $this->modelMock->id();

        $this->assertSame(1, $result);
        $this->assertIsInt($result);
    }

    /**
     * @throws Exception
     */
    public function test_id_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_id')
            ->willReturn(null);

        $result = $this->modelMock->id();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_changeId_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user_id',2)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(2);

        $this->assertInstanceOf(User::class,$result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function test_employeeId_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user__emp_id')
            ->willReturn(1);

        $result = $this->modelMock->employeeId();

        $this->assertSame(1, $result);
        $this->assertIsInt($result);
    }

    /**
     * @throws Exception
     */
    public function test_employeeId_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user__emp_id')
            ->willReturn(null);

        $result = $this->modelMock->employeeId();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_changeEmployeeId_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user__emp_id',2)
            ->willReturnSelf();

        $result = $this->modelMock->changeEmployeeId(2);

        $this->assertInstanceOf(User::class,$result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function test_profileId_should_return_int(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user__pro_id')
            ->willReturn(1);

        $result = $this->modelMock->profileId();

        $this->assertSame(1, $result);
        $this->assertIsInt($result);
    }

    /**
     * @throws Exception
     */
    public function test_profileId_should_return_null(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user__pro_id')
            ->willReturn(null);

        $result = $this->modelMock->profileId();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_changeProfileId_should_return_self(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user__pro_id',2)
            ->willReturnSelf();

        $result = $this->modelMock->changeProfileId(2);

        $this->assertInstanceOf(User::class,$result);
        $this->assertSame($result, $this->modelMock);
    }
}
