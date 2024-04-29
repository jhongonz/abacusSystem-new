<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\Factory;

use Core\User\Application\Factory\UserFactory;
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
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Tests\Feature\Core\User\Application\Factory\DataProvider\DataProviderUserFactory;
use Tests\TestCase;

#[CoversClass(UserFactory::class)]
class UserFactoryTest extends TestCase
{
    private UserFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new UserFactory();
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderUserFactory::class,'provider')]
    public function test_buildUserFromArray_should_return_user(array $dataUser): void
    {
        $result = $this->factory->buildUserFromArray($dataUser);
        $data = $dataUser['user'];

        $this->assertInstanceOf(User::class, $result);

        $this->assertSame($result->id()->value(), $data['id']);
        $this->assertInstanceOf(UserId::class, $result->id());

        $this->assertSame($result->employeeId()->value(), $data['employeeId']);
        $this->assertInstanceOf(UserEmployeeId::class, $result->employeeId());

        $this->assertSame($result->profileId()->value(), $data['profileId']);
        $this->assertInstanceOf(UserProfileId::class, $result->profileId());

        $this->assertSame($result->login()->value(), $data['login']);
        $this->assertInstanceOf(UserLogin::class, $result->login());

        $this->assertSame($result->password()->value(), $data['password']);
        $this->assertInstanceOf(UserPassword::class, $result->password());

        $this->assertSame($result->state()->value(), $data['state']);
        $this->assertInstanceOf(UserState::class, $result->state());

        $this->assertSame(json_decode(json_encode($result->createdAt()->value()),true), $data['createdAt']);
        $this->assertInstanceOf(UserCreatedAt::class, $result->createdAt());

        $this->assertNull($result->updatedAt()->value());
        $this->assertInstanceOf(UserUpdatedAt::class, $result->updatedAt());

        $this->assertSame($result->photo()->value(), $data['photo']);
        $this->assertInstanceOf(UserPhoto::class, $result->photo());
    }

    public function test_buildUpdatedAt_should_return_value_object(): void
    {
        $datetime = new DateTime();

        $result = $this->factory->buildUpdatedAt($datetime);

        $this->assertInstanceOf(UserUpdatedAt::class, $result);
        $this->assertInstanceOf(DateTime::class, $result->value());
        $this->assertSame($datetime, $result->value());
    }

    public function test_buildUserPhoto_should_return_value_object(): void
    {
        $image = 'test.jpg';
        $result = $this->factory->buildUserPhoto($image);

        $this->assertInstanceOf(UserPhoto::class, $result);
        $this->assertSame($image, $result->value());
    }
}
