<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\SearchUser\SearchUserByLoginRequest;
use Core\User\Domain\ValueObjects\UserLogin;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(SearchUserByLoginRequest::class)]
class SearchUserByLoginRequestTest extends TestCase
{
    private UserLogin|Mock $userLogin;
    private SearchUserByLoginRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userLogin = $this->createMock(UserLogin::class);
        $this->request = new SearchUserByLoginRequest($this->userLogin);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->userLogin
        );
        parent::tearDown();
    }

    public function test_userId_should_return_value_object(): void
    {
        $result = $this->request->login();

        $this->assertInstanceOf(UserLogin::class, $result);
        $this->assertSame($this->userLogin, $result);
    }
}
