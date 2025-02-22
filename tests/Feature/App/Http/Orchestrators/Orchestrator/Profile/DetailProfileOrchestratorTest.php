<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\DetailProfileOrchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DetailProfileOrchestrator::class)]
class DetailProfileOrchestratorTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagement;
    private ModuleManagementContract|MockObject $moduleManagement;
    private Config|MockObject $config;
    private DetailProfileOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->config = $this->createMock(Config::class);
        $this->orchestrator = new DetailProfileOrchestrator(
            $this->profileManagement,
            $this->moduleManagement,
            $this->config
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->moduleManagement,
            $this->profileManagement,
            $this->config
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithProfile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('profileId')
            ->willReturn(1);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('modulesAggregator')
            ->willReturn([1]);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock2 = $this->createMock(Module::class);

        $menuKey = $this->createMock(ModuleMenuKey::class);
        $menuKey->expects(self::exactly(2))
            ->method('value')
            ->willReturn('managers');
        $menuKey2 = $this->createMock(ModuleMenuKey::class);
        $menuKey2->expects(self::exactly(2))
            ->method('value')
            ->willReturn('settings');

        $moduleMock->expects(self::exactly(2))
            ->method('menuKey')
            ->willReturn($menuKey);
        $moduleMock2->expects(self::exactly(2))
            ->method('menuKey')
            ->willReturn($menuKey2);

        $stateMock = $this->createMock(ModuleState::class);
        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);
        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $stateMock2 = $this->createMock(ModuleState::class);
        $stateMock2->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);
        $moduleMock2->expects(self::once())
            ->method('state')
            ->willReturn($stateMock2);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $moduleIdMock2 = $this->createMock(ModuleId::class);
        $moduleIdMock2->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleMock2->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock2);

        $modulesMock = new Modules([$moduleMock, $moduleMock2]);

        $this->moduleManagement->expects(self::once())
            ->method('searchModules')
            ->willReturn($modulesMock);

        $expectedConfig = [
            [
                'key' => 'managers',
                'name' => 'Gestión Administrativa',
                'icon' => 'fas fa-tools',
                'route' => null,
            ],
            [
                'key' => 'settings',
                'name' => 'Configuraciones',
                'icon' => 'fas fa-tools',
                'route' => null,
            ],
        ];

        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willreturn($expectedConfig);

        $result = $this->orchestrator->make($requestMock);

        $dataExpected = [
            'profileId' => 1,
            'profile' => $profileMock,
            'modules' => $modulesMock,
            'privileges' => [
                'managers' => [
                    'menu' => [
                        'key' => 'managers',
                        'name' => 'Gestión Administrativa',
                        'icon' => 'fas fa-tools',
                        'route' => null,
                    ],
                    'children' => [
                        ['module' => $moduleMock, 'selected' => true],
                    ],
                ],
                'settings' => [
                    'menu' => [
                        'key' => 'settings',
                        'name' => 'Configuraciones',
                        'icon' => 'fas fa-tools',
                        'route' => null,
                    ],
                    'children' => [
                        ['module' => $moduleMock2, 'selected' => true],
                    ],
                ],
            ],
        ];

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertIsArray($result['privileges']);
        $this->assertCount(2, $result['privileges']);
        $this->assertEquals($dataExpected, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithoutProfile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('profileId')
            ->willReturn(0);

        $this->profileManagement->expects(self::never())
            ->method('searchProfileById');

        $moduleMock = $this->createMock(Module::class);

        $modulesMock = new Modules([$moduleMock]);
        $this->moduleManagement->expects(self::once())
            ->method('searchModules')
            ->willReturn($modulesMock);

        $expectedConfig = [
            [
                'key' => 'managers',
                'name' => 'Gestión Administrativa',
                'icon' => 'fas fa-tools',
                'route' => null,
            ],
        ];

        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willreturn($expectedConfig);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('profileId', $result);
        $this->assertNull($result['profileId']);
        $this->assertArrayHasKey('profile', $result);
        $this->assertNull($result['profile']);
        $this->assertArrayHasKey('modules', $result);
        $this->assertArrayHasKey('privileges', $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('detail-profile', $result);
    }
}
