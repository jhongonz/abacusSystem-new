<?php

namespace Tests\Feature\App\View\Composers;

use App\View\Composers\HomeComposer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(HomeComposer::class)]
class HomeComposerTest extends TestCase
{
    private HomeComposer $composer;

    public function setUp(): void
    {
        parent::setUp();
        $this->composer = new HomeComposer;
    }

    public function tearDown(): void
    {
        unset($this->composer);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_compose_should_return_void(): void
    {
        $view = $this->createMock(\Illuminate\View\View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('versionRandom')
            ->willReturnSelf();

        $this->composer->compose($view);
        $this->assertTrue(true);
    }
}
