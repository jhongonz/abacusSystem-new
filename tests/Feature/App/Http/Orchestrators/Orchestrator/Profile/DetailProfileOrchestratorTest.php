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
            ->method('input')
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

        $menuKey = $this->createMock(ModuleMenuKey::class);
        $menuKey->expects(self::once())
            ->method('value')
            ->willReturn('managers');

        $moduleMock->expects(self::once())
            ->method('menuKey')
            ->willReturn($menuKey);

        $stateMock = $this->createMock(ModuleState::class);
        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);
        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $modulesMock = new Modules([$moduleMock]);

        $this->moduleManagement->expects(self::once())
            ->method('searchModules')
            ->willReturn($modulesMock);

        $expectedConfig = ['managers' => [
            'name' => 'Gestión Administrativa',
            'icon' => 'fas fa-tools',
            'route' => null,
        ]];
        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willreturn($expectedConfig);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('profileId', $result);
        $this->assertSame(1, $result['profileId']);
        $this->assertArrayHasKey('profile', $result);
        $this->assertSame($profileMock, $result['profile']);
        $this->assertArrayHasKey('modules', $result);
        $this->assertArrayHasKey('privileges', $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithoutProfile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(null);

        $this->profileManagement->expects(self::never())
            ->method('searchProfileById');

        $moduleMock = $this->createMock(Module::class);

        $modulesMock = new Modules([$moduleMock]);
        $this->moduleManagement->expects(self::once())
            ->method('searchModules')
            ->willReturn($modulesMock);

        $expectedConfig = ['managers' => [
            'name' => 'Gestión Administrativa',
            'icon' => 'fas fa-tools',
            'route' => null,
        ]];
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
