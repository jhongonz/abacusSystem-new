<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserCreatedAt;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserPassword;
use Core\User\Domain\ValueObjects\UserPhoto;
use Core\User\Domain\ValueObjects\UserProfileId;
use Core\User\Domain\ValueObjects\UserState;
use Core\User\Domain\ValueObjects\UserUpdatedAt;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    private UserId|Mock $userId;

    private UserEmployeeId|Mock $userEmployeeId;

    private UserProfileId|Mock $userProfileId;

    private UserLogin|Mock $userLogin;

    private UserPassword|Mock $userPassword;

    private UserState|Mock $userState;

    private UserCreatedAt|Mock $userCreatedAt;

    private UserUpdatedAt|Mock $userUpdatedAt;

    private UserPhoto|Mock $userPhoto;

    private User $user;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userId = $this->createMock(UserId::class);
        $this->userEmployeeId = $this->createMock(UserEmployeeId::class);
        $this->userProfileId = $this->createMock(UserProfileId::class);
        $this->userLogin = $this->createMock(UserLogin::class);
        $this->userPassword = $this->createMock(UserPassword::class);
        $this->userState = $this->createMock(UserState::class);
        $this->userCreatedAt = $this->createMock(UserCreatedAt::class);
        $this->userUpdatedAt = $this->createMock(UserUpdatedAt::class);
        $this->userPhoto = $this->createMock(UserPhoto::class);

        $this->user = new User(
            $this->userId,
            $this->userEmployeeId,
            $this->userProfileId,
            $this->userLogin,
            $this->userPassword,
            $this->userState,
            $this->userCreatedAt
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->user,
            $this->userId,
            $this->userEmployeeId,
            $this->userProfileId,
            $this->userLogin,
            $this->userPassword,
            $this->userState,
            $this->userCreatedAt,
            $this->userUpdatedAt,
            $this->userPhoto
        );

        parent::tearDown();
    }

    public function test_id_should_return_user_id(): void
    {
        $return = $this->user->id();

        $this->assertInstanceOf(UserId::class, $return);
        $this->assertSame($this->userId, $return);
    }

    public function test_set_id_should_change_id_and_return_object(): void
    {
        $valueObject = new UserId(2);
        $object = $this->user->setId($valueObject);

        $return = $this->user->id();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_employee_id_should_return_employee_id(): void
    {
        $return = $this->user->employeeId();

        $this->assertInstanceOf(UserEmployeeId::class, $return);
        $this->assertSame($this->userEmployeeId, $return);
    }

    public function test_set_employee_id_should_change_id_and_return_object(): void
    {
        $valueObject = new UserEmployeeId(2);
        $object = $this->user->setEmployeeId($valueObject);

        $return = $this->user->employeeId();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_profile_id_should_return_profile_id(): void
    {
        $return = $this->user->profileId();

        $this->assertInstanceOf(UserProfileId::class, $return);
        $this->assertSame($this->userProfileId, $return);
    }

    public function test_set_profile_id_should_change_id_and_return_object(): void
    {
        $valueObject = new UserProfileId(2);
        $object = $this->user->setProfileId($valueObject);

        $return = $this->user->profileId();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_login_should_return_login(): void
    {
        $return = $this->user->login();

        $this->assertInstanceOf(UserLogin::class, $return);
        $this->assertSame($this->userLogin, $return);
    }

    public function test_set_login_should_change_id_and_return_object(): void
    {
        $valueObject = new UserLogin('login');
        $object = $this->user->setLogin($valueObject);

        $return = $this->user->login();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_password_should_return_password(): void
    {
        $return = $this->user->password();

        $this->assertInstanceOf(UserPassword::class, $return);
        $this->assertSame($this->userPassword, $return);
    }

    public function test_set_password_should_change_id_and_return_object(): void
    {
        $valueObject = new UserPassword('login');
        $object = $this->user->setPassword($valueObject);

        $return = $this->user->password();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_state_should_return_state(): void
    {
        $return = $this->user->state();

        $this->assertInstanceOf(UserState::class, $return);
        $this->assertSame($this->userState, $return);
    }

    public function test_set_state_should_change_id_and_return_object(): void
    {
        $valueObject = new UserState();
        $object = $this->user->setState($valueObject);

        $return = $this->user->state();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_created_at_should_return_created_at(): void
    {
        $return = $this->user->createdAt();

        $this->assertInstanceOf(UserCreatedAt::class, $return);
        $this->assertSame($this->userCreatedAt, $return);
    }

    public function test_set_created_at_should_change_id_and_return_object(): void
    {
        $valueObject = new UserCreatedAt();
        $object = $this->user->setCreatedAt($valueObject);

        $return = $this->user->createdAt();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_updated_at_should_return_updated_at(): void
    {
        $this->user->setUpdatedAt($this->userUpdatedAt);
        $return = $this->user->updatedAt();

        $this->assertInstanceOf(UserUpdatedAt::class, $return);
        $this->assertSame($this->userUpdatedAt, $return);
    }

    public function test_set_updated_at_should_change_id_and_return_object(): void
    {
        $valueObject = new UserUpdatedAt();
        $object = $this->user->setUpdatedAt($valueObject);

        $return = $this->user->updatedAt();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }

    public function test_photo_should_return_photo(): void
    {
        $this->user->setPhoto($this->userPhoto);
        $return = $this->user->photo();

        $this->assertInstanceOf(UserPhoto::class, $return);
        $this->assertSame($this->userPhoto, $return);
    }

    public function test_set_photo_should_change_id_and_return_object(): void
    {
        $valueObject = new UserPhoto();
        $object = $this->user->setPhoto($valueObject);

        $return = $this->user->photo();

        $this->assertSame($valueObject, $return);
        $this->assertInstanceOf(User::class, $object);
    }
}
