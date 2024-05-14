<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Infrastructure\Persistence\Translators;

use Core\User\Domain\Contracts\UserFactoryContract;
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
use Core\User\Infrastructure\Persistence\Eloquent\Model\User as UserModel;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UserTranslator::class)]
class UserTranslatorTest extends TestCase
{
    private UserFactoryContract|MockObject $userFactory;

    private UserModel|MockObject $userModel;

    private UserTranslator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userFactory = $this->createMock(UserFactoryContract::class);
        $this->userModel = $this->createMock(UserModel::class);
        $this->translator = new UserTranslator(
            $this->userFactory,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->userFactory,
            $this->userModel,
            $this->translator
        );
        parent::tearDown();
    }

    public function test_setModel_should_return_self(): void
    {
        $return = $this->translator->setModel($this->userModel);

        $this->assertInstanceOf(UserTranslator::class, $return);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_toDomain_should_return_user_class(): void
    {
        $this->userModel->expects(self::once())
            ->method('id')
            ->willReturn(1);

        $userIdMock = $this->createMock(UserId::class);
        $this->userFactory->expects(self::once())
            ->method('buildId')
            ->willReturn($userIdMock);

        $this->userModel->expects(self::once())
            ->method('employeeId')
            ->willReturn(1);

        $employeeIdMock = $this->createMock(UserEmployeeId::class);
        $this->userFactory->expects(self::once())
            ->method('buildEmployeeId')
            ->willReturn($employeeIdMock);

        $this->userModel->expects(self::once())
            ->method('profileId')
            ->willReturn(1);

        $profileIdMock = $this->createMock(UserProfileId::class);
        $this->userFactory->expects(self::once())
            ->method('buildProfileId')
            ->willReturn($profileIdMock);

        $this->userModel->expects(self::once())
            ->method('login')
            ->willReturn('email');

        $loginMock = $this->createMock(UserLogin::class);
        $this->userFactory->expects(self::once())
            ->method('buildLogin')
            ->willReturn($loginMock);

        $this->userModel->expects(self::once())
            ->method('password')
            ->willReturn('12345');

        $passwordMock = $this->createMock(UserPassword::class);
        $this->userFactory->expects(self::once())
            ->method('buildPassword')
            ->willReturn($passwordMock);

        $this->userModel->expects(self::once())
            ->method('state')
            ->willReturn(2);

        $stateMock = $this->createMock(UserState::class);
        $this->userFactory->expects(self::once())
            ->method('buildState')
            ->willReturn($stateMock);

        $datetime = new \DateTime('2024-04-22 22:05:00');
        $this->userModel->expects(self::once())
            ->method('createdAt')
            ->willReturn($datetime);

        $createdAtMock = $this->createMock(UserCreatedAt::class);
        $this->userFactory->expects(self::once())
            ->method('buildCreatedAt')
            ->willReturn($createdAtMock);

        $this->userModel->expects(self::once())
            ->method('photo')
            ->willReturn('image.jpg');

        $photoMock = $this->createMock(UserPhoto::class);
        $this->userFactory->expects(self::once())
            ->method('buildUserPhoto')
            ->with('image.jpg')
            ->willReturn($photoMock);

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::once())
            ->method('setPhoto')
            ->with($photoMock)
            ->willReturnSelf();

        $this->userModel->expects(self::once())
            ->method('updatedAt')
            ->willReturn($datetime);

        $updatedAt = $this->createMock(UserUpdatedAt::class);
        $this->userFactory->expects(self::once())
            ->method('buildUpdatedAt')
            ->willReturn($updatedAt);

        $userMock->expects(self::once())
            ->method('setUpdatedAt')
            ->with($updatedAt)
            ->willReturnSelf();

        $this->userFactory->expects(self::once())
            ->method('buildUser')
            ->with(
                $userIdMock,
                $employeeIdMock,
                $profileIdMock,
                $loginMock,
                $passwordMock,
                $stateMock,
                $createdAtMock
            )
            ->willReturn($userMock);

        $this->translator->setModel($this->userModel);
        $result = $this->translator->toDomain();

        $this->assertInstanceOf(User::class, $result);
    }
}
