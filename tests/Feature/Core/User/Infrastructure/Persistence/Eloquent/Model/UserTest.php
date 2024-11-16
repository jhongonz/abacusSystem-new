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

    public function testGetSearchFieldShouldReturnString(): void
    {
        $expected = 'user_login';
        $result = $this->model->getSearchField();

        $this->assertSame($expected, $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function testRelationWithEmployeeShouldReturnBelongsTo(): void
    {
        $relationMock = $this->createMock(BelongsTo::class);

        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['belongsTo', 'newBelongsTo', 'newRelatedInstance', 'guessBelongsToRelation'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('belongsTo')
            ->with(Employee::class, 'user__emp_id', 'emp_id')
            ->willReturn($relationMock);

        $result = $this->modelMock->relationWithEmployee();

        $this->assertInstanceOf(BelongsTo::class, $result);
        $this->assertSame($relationMock, $result);
    }

    public function testEmployeeShouldReturnEmployeeModel(): void
    {
        $result = $this->model->employee();
        $this->assertInstanceOf(Employee::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testRelationWithProfileShouldReturnBelongsTo(): void
    {
        $relationMock = $this->createMock(BelongsTo::class);

        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['belongsTo', 'newBelongsTo', 'newRelatedInstance', 'guessBelongsToRelation'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('belongsTo')
            ->with(Profile::class, 'user__pro_id', 'pro_id')
            ->willReturn($relationMock);

        $result = $this->modelMock->relationWithProfile();

        $this->assertInstanceOf(BelongsTo::class, $result);
        $this->assertSame($relationMock, $result);
    }

    public function testProfileShouldReturnProfileModel(): void
    {
        $result = $this->model->profile();
        $this->assertInstanceOf(Profile::class, $result);
    }

    /**
     * @throws Exception
     */
    public function testIdShouldReturnInt(): void
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
    public function testIdShouldReturnNull(): void
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
    public function testChangeIdShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeId(2);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function testEmployeeIdShouldReturnInt(): void
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
    public function testEmployeeIdShouldReturnNull(): void
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
    public function testChangeEmployeeIdShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user__emp_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeEmployeeId(2);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function testProfileIdShouldReturnInt(): void
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
    public function testProfileIdShouldReturnNull(): void
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
    public function testChangeProfileIdShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user__pro_id', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeProfileId(2);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function testLoginShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_login')
            ->willReturn('login');

        $result = $this->modelMock->login();

        $this->assertSame('login', $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function testLoginShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_login')
            ->willReturn(null);

        $result = $this->modelMock->login();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testChangeLoginShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user_login', 'login-2')
            ->willReturnSelf();

        $result = $this->modelMock->changeLogin('login-2');

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function testPasswordShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('password')
            ->willReturn('12345');

        $result = $this->modelMock->password();

        $this->assertSame('12345', $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function testPasswordShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('password')
            ->willReturn(null);

        $result = $this->modelMock->password();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testChangePasswordShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('password', '54321')
            ->willReturnSelf();

        $result = $this->modelMock->changePassword('54321');

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function testStateShouldReturnInt(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_state')
            ->willReturn(1);

        $result = $this->modelMock->state();

        $this->assertSame(1, $result);
        $this->assertIsInt($result);
    }

    /**
     * @throws Exception
     */
    public function testChangeStateShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user_state', 2)
            ->willReturnSelf();

        $result = $this->modelMock->changeState(2);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreatedAtShouldReturnDatetime(): void
    {
        $datetimeString = '2024-04-26 06:29:00';
        $expected = new \DateTime($datetimeString);
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn($datetimeString);

        $result = $this->modelMock->createdAt();

        $this->assertSame($datetimeString, $result->format('Y-m-d h:i:s'));
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreatedAtShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('created_at')
            ->willReturn(null);

        $result = $this->modelMock->createdAt();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testChangeCreatedAtShouldReturnSelf(): void
    {
        $datetime = new \DateTime('2024-04-26 6:35:00');
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('created_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeCreatedAt($datetime);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testUpdatedAtShouldReturnDatetime(): void
    {
        $datetimeString = '2024-04-26 06:40:00';
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn($datetimeString);

        $result = $this->modelMock->updatedAt();

        $this->assertSame($datetimeString, $result->format('Y-m-d h:i:s'));
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testUpdatedAtShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('updated_at')
            ->willReturn(null);

        $result = $this->modelMock->updatedAt();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testChangeUpdatedAtShouldReturnSelf(): void
    {
        $datetime = new \DateTime('2024-04-26 6:45:00');
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('updated_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeUpdatedAt($datetime);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testDeletedAtShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('deleted_at')
            ->willReturn(null);

        $result = $this->modelMock->deletedAt();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testChangeDeletedAtShouldReturnSelf(): void
    {
        $datetime = new \DateTime('2024-04-26 6:45:00');
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('deleted_at', $datetime)
            ->willReturnSelf();

        $result = $this->modelMock->changeDeletedAt($datetime);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }

    /**
     * @throws Exception
     */
    public function testPhotoShouldReturnString(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_photo')
            ->willReturn('image.jpg');

        $result = $this->modelMock->photo();

        $this->assertSame('image.jpg', $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function testPhotoShouldReturnNull(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('getAttribute')
            ->with('user_photo')
            ->willReturn(null);

        $result = $this->modelMock->photo();

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testChangePhotoShouldReturnSelf(): void
    {
        $this->modelMock = $this->getMockBuilder(User::class)
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->modelMock->expects(self::once())
            ->method('setAttribute')
            ->with('user_photo', 'photo.jpg')
            ->willReturnSelf();

        $result = $this->modelMock->changePhoto('photo.jpg');

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($result, $this->modelMock);
    }
}
