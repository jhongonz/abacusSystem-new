<?php

namespace Tests\Feature\App\View\Composers;

use App\View\Composers\HomeComposer;
use Illuminate\Support\Str;
use Illuminate\View\View;
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
        $this->composer = new HomeComposer();
    }

    public function tearDown(): void
    {
        unset($this->composer);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testComposeShouldReturnVoid(): void
    {
        Str::createRandomStringsUsing(function () {
            return 'OLl3rUybNy';
        });

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('versionRandom', 'OLl3rUybNy')
            ->willReturnSelf();

        $this->composer->compose($view);
        $this->assertTrue(true);
    }
}
