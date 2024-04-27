<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\DeleteUser;

use Core\User\Application\UseCases\DeleteUser\DeleteUserRequest;
use Core\User\Domain\ValueObjects\UserId;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(DeleteUserRequest::class)]
class DeleteUserRequestTest extends TestCase
{
    private UserId|Mock $userId;
    private DeleteUserRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userId = $this->createMock(UserId::class);
        $this->request = new DeleteUserRequest($this->userId);
    }

    public function tearDown(): void
    {
        unset($this->request, $this->userId);
        parent::tearDown();
    }

    public function test_userId_should_return_value_object(): void
    {
        $result = $this->request->userId();

        $this->assertSame($result, $this->userId);
        $this->assertInstanceOf(UserId::class, $result);
    }
}
