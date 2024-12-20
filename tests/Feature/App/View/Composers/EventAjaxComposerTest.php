<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-19 21:33:38
 */

namespace Tests\Feature\App\View\Composers;

use App\View\Composers\EventAjaxComposer;
use App\View\Composers\MenuComposer;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventAjaxComposer::class)]
class EventAjaxComposerTest extends TestCase
{
    private Factory|MockObject $viewFactory;
    private Request|MockObject $request;
    private Session|MockObject $session;
    private EventAjaxComposer $composer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewFactory = $this->createMock(Factory::class);
        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(Session::class);

        $this->composer = new EventAjaxComposer(
            $this->viewFactory,
            $this->request,
            $this->session
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->viewFactory,
            $this->request,
            $this->session,
            $this->composer
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testComposerShouldComposeViewWhenRequestIsAjax(): void
    {
        $this->request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);

        $this->session->expects(self::once())
            ->method('get')
            ->with('user')
            ->willReturn([]);

        $this->viewFactory->expects(self::never())
            ->method('composer');

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('with')
            ->with('layout', 'layouts.home-ajax')
            ->willReturnSelf();

        $this->composer->compose($viewMock);
    }

    /**
     * @throws Exception
     */
    public function testComposerShouldComposeViewWhenRequestIsNotAjax(): void
    {
        $this->request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);

        $this->session->expects(self::once())
            ->method('get')
            ->with('user')
            ->willReturn([]);

        $this->viewFactory->expects(self::once())
            ->method('composer')
            ->with('layouts.menu', MenuComposer::class)
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::once())
            ->method('with')
            ->with('layout', 'layouts.home')
            ->willReturnSelf();

        $this->composer->compose($viewMock);
    }
}
