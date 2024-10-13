<?php

namespace Tests\Feature\App\View\Composers;

use App\View\Composers\MenuComposer;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserPhoto;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(MenuComposer::class)]
class MenuComposerTest extends TestCase
{
    private ModuleFactoryContract|MockObject $moduleFactory;
    private Config|MockObject $config;
    private Router|MockObject $router;
    private Session|MockObject $session;
    private UrlGenerator $urlGenerator;
    private MenuComposer $composer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleFactory = $this->createMock(ModuleFactoryContract::class);
        $this->config = $this->createMock(Config::class);
        $this->router = $this->createMock(Router::class);
        $this->session = $this->createMock(Session::class);
        $this->urlGenerator = $this->createMock(UrlGenerator::class);

        $this->composer = new MenuComposer(
            $this->moduleFactory,
            $this->config,
            $this->router,
            $this->session,
            $this->urlGenerator
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->composer,
            $this->moduleFactory,
            $this->config,
            $this->session,
            $this->router,
            $this->urlGenerator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_compose_should_prepare_menu(): void
    {
        $userMock = $this->createMock(User::class);

        $photoMock = $this->createMock(UserPhoto::class);
        $photoMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');
        $userMock->expects(self::once())
            ->method('photo')
            ->willReturn($photoMock);

        $profileMock = $this->createMock(Profile::class);
        $moduleMock = $this->createMock(Module::class);

        $menuKeyMock = $this->createMock(ModuleMenuKey::class);
        $menuKeyMock->expects(self::once())
            ->method('value')
            ->willReturn('managers');
        $moduleMock->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKeyMock);

        $moduleRouteMock = $this->createMock(ModuleRoute::class);
        $moduleRouteMock->expects(self::once())
            ->method('value')
            ->willReturn('localhost');
        $moduleMock->expects(self::once())
            ->method('route')
            ->willReturn($moduleRouteMock);

        $modulesMock = new Modules([$moduleMock]);

        $configExpected = [
            'managers' => [
                'name' => 'Gestión Administrativa',
                'icon' => 'fas fa-tools',
                'route' => null,
            ],
            'setting' => [
                'name' => 'Testing',
                'icon' => 'fas fa-tools',
                'route' => 'testing',
            ]
        ];
        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willReturn($configExpected);

        $profileMock->expects(self::once())
            ->method('modules')
            ->willReturn($modulesMock);

        $employeeMock = $this->createMock(Employee::class);

        $this->session->expects(self::exactly(3))
            ->method('get')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                $userMock,
                $profileMock,
                $employeeMock
            );

        $this->moduleFactory->expects(self::exactly(2))
            ->method('buildModuleFromArray')
            ->withAnyParameters()
            ->willReturn($moduleMock);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('localhost');

        $this->router->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);

        $this->urlGenerator->expects(self::once())
            ->method('to')
            ->withAnyParameters()
            ->willReturn('http://localhost');

        $viewMock = $this->createMock(View::class);
        $viewMock->expects(self::exactly(5))
            ->method('with')
            ->withAnyParameters()
            ->willReturnSelf();

        $this->composer->compose($viewMock);
        $this->assertTrue(true);
    }
}
