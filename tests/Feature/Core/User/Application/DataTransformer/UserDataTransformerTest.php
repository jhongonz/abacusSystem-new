<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\DataTransformer;

use Core\User\Application\DataTransformer\UserDataTransformer;
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
use DateTime;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\User\Application\DataTransformer\DataProvider\DataProviderDataTransformer;
use Tests\TestCase;

#[CoversClass(UserDataTransformer::class)]
class UserDataTransformerTest extends TestCase
{
    private User|Mock $user;
    private UserDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createMock(User::class);
        $this->dataTransformer = new UserDataTransformer();
    }

    public function tearDown(): void
    {
        unset($this->user, $this->dataTransformer);
        parent::tearDown();
    }

    public function test_write_should_return_object(): void
    {
        $result = $this->dataTransformer->write($this->user);

        $this->assertInstanceOf(UserDataTransformer::class, $result);
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderDataTransformer::class,'provider')]
    public function test_read_should_return_array(array $expected, DateTime $datetime): void
    {
        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->user->method('id')->willReturn($userIdMock);

        $userEmployeeIdMock = $this->createMock(UserEmployeeId::class);
        $userEmployeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->user->method('employeeId')->willReturn($userEmployeeIdMock);

        $userProfileIdMock = $this->createMock(UserProfileId::class);
        $userProfileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->user->method('profileId')->willReturn($userProfileIdMock);

        $userLoginMock = $this->createMock(UserLogin::class);
        $userLoginMock->expects(self::once())
            ->method('value')
            ->willReturn('login');
        $this->user->method('login')->willReturn($userLoginMock);

        $userPasswordMock = $this->createMock(UserPassword::class);
        $userPasswordMock->expects(self::once())
            ->method('value')
            ->willReturn('12345');
        $this->user->method('password')->willReturn($userPasswordMock);

        $userStateMock = $this->createMock(UserState::class);
        $userStateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->user->method('state')->willReturn($userStateMock);

        $userPhotoMock = $this->createMock(UserPhoto::class);
        $userPhotoMock->expects(self::once())
            ->method('value')
            ->willReturn('test.jpg');
        $this->user->method('photo')->willReturn($userPhotoMock);

        $userCreatedAtMock = $this->createMock(UserCreatedAt::class);
        $userCreatedAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $this->user->method('createdAt')->willReturn($userCreatedAtMock);

        $userUpdatedAtMock = $this->createMock(UserUpdatedAt::class);
        $userUpdatedAtMock->expects(self::once())
            ->method('value')
            ->willReturn($datetime);
        $this->user->method('updatedAt')->willReturn($userUpdatedAtMock);

        $this->dataTransformer->write($this->user);
        $result = $this->dataTransformer->read();

        $this->assertSame($expected, $result);
        $this->assertIsArray($result);
        $this->assertArrayHasKey(User::TYPE, $result);
    }
}
