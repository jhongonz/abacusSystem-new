<?php

namespace Tests\Feature\App\View\Composers;

use App\View\Composers\MenuComposer;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserPhoto;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\App\View\Composers\DataProvider\MenuComposerDataProvider;
use Tests\TestCase;

#[CoversClass(MenuComposer::class)]
class MenuComposerTest extends TestCase
{
    private ModuleFactoryContract|MockObject $moduleFactory;
    private Config|MockObject $config;
    private Router|MockObject $router;
    private Session|MockObject $session;
    private UrlGenerator|MockObject $urlGenerator;
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
     * @param array<int, array<string, string>> $optionsExpected
     *
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(MenuComposerDataProvider::class, 'provider_menuOptionsParent')]
    public function testComposeShouldPrepareMenuWithOptionsParent(array $optionsExpected): void
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
        $menuKeyMock = $this->createMock(ModuleMenuKey::class);

        $callIndexKey = 0;
        $menuKeyMock->expects(self::exactly(2))
            ->method('value')
            ->willReturnCallback(function () use (&$callIndexKey) {
                $result = null;
                if (0 === $callIndexKey) {
                    $result = 'manager';
                } elseif (1 === $callIndexKey) {
                    $result = 'settings';
                }

                ++$callIndexKey;

                return $result;
            });

        $moduleFirstMock = $this->createMock(Module::class);
        $moduleFirstIdMock = $this->createMock(ModuleId::class);

        $moduleFirstIdMock->expects(self::once())
            ->method('setValue')
            ->with(0)
            ->willReturnSelf();
        $moduleFirstMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleFirstIdMock);

        $moduleFirstSateMock = $this->createMock(ModuleState::class);
        $moduleFirstSateMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();
        $moduleFirstMock->expects(self::once())
            ->method('state')
            ->willReturn($moduleFirstSateMock);

        $moduleFirstMock->expects(self::exactly(2))
            ->method('menuKey')
            ->willReturn($menuKeyMock);

        $moduleFirstMock->expects(self::once())
            ->method('isParent')
            ->willReturn(true);

        $moduleRouteMock = $this->createMock(ModuleRoute::class);
        $moduleRouteMock->expects(self::once())
            ->method('value')
            ->willReturn('localhost');

        $moduleRouteMock->expects(self::once())
            ->method('setValue')
            ->with('')
            ->willReturnSelf();

        $moduleFirstMock->expects(self::exactly(2))
            ->method('route')
            ->willReturn($moduleRouteMock);

        $moduleFirstMock->expects(self::exactly(2))
            ->method('setExpanded')
            ->with(true)
            ->willReturnSelf();

        $moduleFirstMock->expects(self::once())
            ->method('setOptions')
            ->willReturnSelf();

        $moduleSecondMock = $this->createMock(Module::class);
        $moduleSecondIdMock = $this->createMock(ModuleId::class);

        $moduleSecondIdMock->expects(self::once())
            ->method('setValue')
            ->with(0)
            ->willReturnSelf();
        $moduleSecondMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleSecondIdMock);

        $moduleSecondSateMock = $this->createMock(ModuleState::class);
        $moduleSecondSateMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();
        $moduleSecondMock->expects(self::once())
            ->method('state')
            ->willReturn($moduleSecondSateMock);

        $moduleSecondMock->expects(self::exactly(2))
            ->method('menuKey')
            ->willReturn($menuKeyMock);

        $moduleSecondMock->expects(self::once())
            ->method('isParent')
            ->willReturn(false);

        $modulesMock = $this->createMock(Modules::class);
        $modulesMock->addItem($moduleFirstMock);
        $modulesMock->addItem($moduleSecondMock);

        $modulesMock->expects(self::once())
            ->method('moduleElementsOfKey')
            ->willReturn([$moduleFirstMock, $moduleSecondMock]);

        $profileMock->expects(self::once())
            ->method('modules')
            ->willReturn($modulesMock);

        $employeeMock = $this->createMock(Employee::class);

        $this->session->expects(self::exactly(3))
            ->method('get')
            ->willReturnCallback(function (string $key) use ($userMock, $profileMock, $employeeMock) {
                $result = null;
                if ('user' === $key) {
                    $result = $userMock;
                } elseif ('profile' === $key) {
                    $result = $profileMock;
                } elseif ('employee' === $key) {
                    $result = $employeeMock;
                }

                return $result;
            });

        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willReturn($optionsExpected);

        $callIndexFactory = 0;
        $this->moduleFactory->expects(self::exactly(2))
            ->method('buildModuleFromArray')
            ->withAnyParameters()
            ->willReturnCallback(function (array $dataModule) use ($moduleFirstMock, $moduleSecondMock, $optionsExpected, &$callIndexFactory) {
                $this->assertEquals([Module::TYPE => $optionsExpected[$callIndexFactory]], $dataModule);

                $result = null;
                $result = null;
                if (0 === $callIndexFactory) {
                    $result = $moduleFirstMock;
                } elseif (1 === $callIndexFactory) {
                    $result = $moduleSecondMock;
                }

                ++$callIndexFactory;

                return $result;
            });

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

        $callIndexView = 0;
        $viewMock->expects(self::exactly(5))
            ->method('with')
            ->withAnyParameters()
            ->willReturnCallback(function (string $key, mixed $data) use (
                $moduleFirstMock, $moduleSecondMock, $userMock, $employeeMock, $profileMock, &$callIndexView
            ) {
                if ('menu' === $key) {
                    $this->assertIsArray($data);
                    $this->assertContainsOnlyInstancesOf(Module::class, $data);
                    $this->assertEquals([$moduleFirstMock, $moduleSecondMock], $data);
                } elseif ('user' === $key) {
                    $this->assertInstanceOf(User::class, $data);
                    $this->assertEquals($userMock, $data);
                } elseif ('employee' === $key) {
                    $this->assertInstanceOf(Employee::class, $data);
                    $this->assertEquals($employeeMock, $data);
                } elseif ('profile' === $key) {
                    $this->assertInstanceOf(Profile::class, $data);
                    $this->assertEquals($profileMock, $data);
                } elseif ('image' == $key) {
                    $this->assertIsString($data);
                    $this->assertEquals('http://localhost', $data);
                }

                ++$callIndexView;
            });

        $this->composer->compose($viewMock);
    }
}
