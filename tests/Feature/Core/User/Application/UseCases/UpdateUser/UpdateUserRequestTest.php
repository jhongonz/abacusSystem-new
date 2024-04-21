<?php

namespace Tests\Feature\Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\UpdateUser\UpdateUserRequest;
use Core\User\Domain\ValueObjects\UserId;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(UpdateUserRequest::class)]
class UpdateUserRequestTest extends TestCase
{
    private UserId|Mock $userId;
    private array $data;
    private UpdateUserRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userId = $this->createMock(UserId::class);
        $this->data = [];
        $this->request = new UpdateUserRequest($this->userId, $this->data);
    }

    public function tearDown(): void
    {
        unset($this->data, $this->userId, $this->request);
        parent::tearDown();
    }

    public function test_userId_should_return_value_object(): void
    {
        $result = $this->request->userId();

        $this->assertInstanceOf(UserId::class, $result);
        $this->assertSame($result, $this->userId);
    }

    public function test_data_should_return_array(): void
    {
        $result = $this->request->data();

        $this->assertIsArray($result);
        $this->assertSame($result,[]);
    }
}
