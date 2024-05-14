<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\CreateUser;

use Core\User\Application\UseCases\CreateUser\CreateUserRequest;
use Core\User\Domain\User;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(CreateUserRequest::class)]
class CreateUserRequestTest extends TestCase
{
    private User|Mock $user;

    private CreateUserRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createMock(User::class);
        $this->request = new CreateUserRequest($this->user);
    }

    public function tearDown(): void
    {
        unset($this->user, $this->request);
        parent::tearDown();
    }

    public function test_user_should_return_user_domain(): void
    {
        $result = $this->request->user();

        $this->assertSame($this->user, $result);
        $this->assertInstanceOf(User::class, $result);
    }
}
