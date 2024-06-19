<?php

namespace Tests\Feature\Core\Profile\Application\DataTransformer;

use Core\Profile\Application\DataTransformer\ModuleDataTransformer;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModulePosition;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Profile\Application\DataTransformer\DataProvider\DataProviderDataTransformer;
use Tests\TestCase;

#[CoversClass(ModuleDataTransformer::class)]
class ModuleDataTransformerTest extends TestCase
{
    private Module|MockObject $module;

    private ModuleDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->module = $this->createMock(Module::class);
        $this->dataTransformer = new ModuleDataTransformer;
    }

    public function tearDown(): void
    {
        unset(
            $this->dataTransformer,
            $this->module
        );
        parent::tearDown();
    }

    public function test_write_should_return_self(): void
    {
        $result = $this->dataTransformer->write($this->module);

        $this->assertInstanceOf(ModuleDataTransformer::class, $result);
        $this->assertSame($result, $this->dataTransformer);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderDataTransformer::class, 'providerModuleToRead')]
    public function test_read_should_return_array(array $expected, string $datetime): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->module->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $moduleKeyMock = $this->createMock(ModuleMenuKey::class);
        $moduleKeyMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('menuKey')
            ->willReturn($moduleKeyMock);

        $moduleName = $this->createMock(ModuleName::class);
        $moduleName->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('name')
            ->willReturn($moduleName);

        $moduleRoute = $this->createMock(ModuleRoute::class);
        $moduleRoute->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('route')
            ->willReturn($moduleRoute);

        $moduleIcon = $this->createMock(ModuleIcon::class);
        $moduleIcon->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('icon')
            ->willReturn($moduleIcon);

        $moduleState = $this->createMock(ModuleState::class);
        $moduleState->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->module->expects(self::once())
            ->method('state')
            ->willReturn($moduleState);

        $modulePosition = $this->createMock(ModulePosition::class);
        $modulePosition->expects(self::once())
            ->method('value')
            ->willReturn(2);
        $this->module->expects(self::once())
            ->method('position')
            ->willReturn($modulePosition);

        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn(new \DateTime($datetime));
        $this->module->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('value')
            ->willReturn(new \DateTime($datetime));
        $this->module->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->dataTransformer->write($this->module);
        $result = $this->dataTransformer->read();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('module', $result);
        $this->assertSame($expected, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderDataTransformer::class, 'providerModuleToReadToShare')]
    public function test_readToShare_should_return_array(array $expected, string $datetime): void
    {
        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $this->module->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $moduleKeyMock = $this->createMock(ModuleMenuKey::class);
        $moduleKeyMock->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('menuKey')
            ->willReturn($moduleKeyMock);

        $moduleName = $this->createMock(ModuleName::class);
        $moduleName->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('name')
            ->willReturn($moduleName);

        $moduleRoute = $this->createMock(ModuleRoute::class);
        $moduleRoute->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('route')
            ->willReturn($moduleRoute);

        $moduleIcon = $this->createMock(ModuleIcon::class);
        $moduleIcon->expects(self::once())
            ->method('value')
            ->willReturn('test');
        $this->module->expects(self::once())
            ->method('icon')
            ->willReturn($moduleIcon);

        $moduleState = $this->createMock(ModuleState::class);
        $moduleState->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleState->expects(self::once())
            ->method('formatHtmlToState')
            ->willReturn('test');

        $this->module->expects(self::exactly(2))
            ->method('state')
            ->willReturn($moduleState);

        $modulePosition = $this->createMock(ModulePosition::class);
        $modulePosition->expects(self::once())
            ->method('value')
            ->willReturn(2);
        $this->module->expects(self::once())
            ->method('position')
            ->willReturn($modulePosition);

        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('value')
            ->willReturn(new \DateTime($datetime));
        $this->module->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('value')
            ->willReturn(new \DateTime($datetime));
        $this->module->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->dataTransformer->write($this->module);
        $result = $this->dataTransformer->readToShare();

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey(Module::TYPE, $result);
        $this->assertSame($expected, $result);
    }
}
