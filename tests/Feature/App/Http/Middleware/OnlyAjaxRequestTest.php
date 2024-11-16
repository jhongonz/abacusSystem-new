<?php

namespace Tests\Feature\App\Http\Middleware;

use App\Http\Middleware\OnlyAjaxRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(OnlyAjaxRequest::class)]
class OnlyAjaxRequestTest extends TestCase
{
    private Redirector|MockObject $redirectorMock;
    private OnlyAjaxRequest $middleware;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->redirectorMock = $this->createMock(Redirector::class);
        $this->middleware = new OnlyAjaxRequest($this->redirectorMock);
    }

    protected function tearDown(): void
    {
        unset(
            $this->redirectorMock,
            $this->middleware
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldReturnRedirectResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $responseMock = $this->createMock(RedirectResponse::class);
        $this->redirectorMock->expects(self::once())
            ->method('route')
            ->with('index')
            ->willReturn($responseMock);

        $result = $this->middleware->handle($requestMock, function ($request) {
            return new Response();
        });

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldReturnResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $this->redirectorMock->expects(self::never())
            ->method('route');

        $result = $this->middleware->handle($requestMock, function () {
            return new Response('OK', \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        });

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('OK', $result->getContent());
    }
}
