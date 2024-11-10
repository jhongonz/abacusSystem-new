<?php

namespace Tests\Feature\App\Http\Middleware;

use App\Http\Middleware\VerifySession;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(VerifySession::class)]
class VerifySessionTest extends TestCase
{
    private Redirector|MockObject $redirectorMock;
    private Session|MockObject $sessionMock;
    private VerifySession $middleware;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->redirectorMock = $this->createMock(Redirector::class);
        $this->sessionMock = $this->createMock(Session::class);
        $this->middleware = new VerifySession($this->redirectorMock, $this->sessionMock);
    }

    protected function tearDown(): void
    {
        unset(
            $this->redirectorMock,
            $this->sessionMock,
            $this->middleware
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_handle_should_return_json_response(): void
    {
        $this->sessionMock->expects(self::once())
            ->method('exists')
            ->with(['user', 'profile', 'employee'])
            ->willReturn(false);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $result = $this->middleware->handle($requestMock, function ($request) {
            return new Response;
        });

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        $this->assertArrayHasKey('error', json_decode($result->getContent(), true));
        $this->assertArrayHasKey('error_description', json_decode($result->getContent(), true));
        $this->assertArrayHasKey('reason', json_decode($result->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function test_handle_should_return_redirect_response(): void
    {
        $this->sessionMock->expects(self::once())
            ->method('exists')
            ->with(['user', 'profile', 'employee'])
            ->willReturn(false);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $responseMock = $this->createMock(RedirectResponse::class);
        $this->redirectorMock->expects(self::once())
            ->method('to')
            ->with('/')
            ->willReturn($responseMock);

        $result = $this->middleware->handle($requestMock, function ($request) {
            return new Response;
        });

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_handle_should_return_response(): void
    {
        $this->sessionMock->expects(self::once())
            ->method('exists')
            ->with(['user', 'profile', 'employee'])
            ->willReturn(true);

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::never())
            ->method('ajax');

        $this->redirectorMock->expects(self::never())
            ->method('to');

        $result = $this->middleware->handle($requestMock, function () {
            return new Response('OK', \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        });

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals('OK', $result->getContent());
    }
}
