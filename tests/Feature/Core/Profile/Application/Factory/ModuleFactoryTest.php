<?php

namespace Tests\Feature\Core\Profile\Application\Factory;

use Core\Profile\Application\Factory\ModuleFactory;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModulePermission;
use Core\Profile\Domain\ValueObjects\ModulePosition;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Profile\Application\Factory\DataProvider\DataProviderFactory;
use Tests\TestCase;

#[CoversClass(ModuleFactory::class)]
class ModuleFactoryTest extends TestCase
{
    private ModuleFactory|MockObject $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new ModuleFactory();
    }

    public function tearDown(): void
    {
        unset($this->factory);
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $dataObject
     *
     * @throws \Exception
     * @throws Exception
     */
    #[DataProviderExternal(DataProviderFactory::class, 'providerModule')]
    public function testBuildModuleFromArrayShouldReturnModuleObject(array $dataObject): void
    {
        $dataProvider = $dataObject[Module::TYPE];
        $this->factory = $this->getMockBuilder(ModuleFactory::class)
            ->onlyMethods([
                'buildModule',
                'buildModuleId',
                'buildModuleMenuKey',
                'buildModuleName',
                'buildModuleRoute',
            ])
            ->getMock();

        $idMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with($dataProvider['id'])
            ->willReturn($idMock);

        $keyMock = $this->createMock(ModuleMenuKey::class);
        $this->factory->expects(self::once())
            ->method('buildModuleMenuKey')
            ->with($dataProvider['key'])
            ->willReturn($keyMock);

        $nameMock = $this->createMock(ModuleName::class);
        $this->factory->expects(self::once())
            ->method('buildModuleName')
            ->with($dataProvider['name'])
            ->willReturn($nameMock);

        $routeMock = $this->createMock(ModuleRoute::class);
        $this->factory->expects(self::once())
            ->method('buildModuleRoute')
            ->with($dataProvider['route'])
            ->willReturn($routeMock);

        $moduleMock = $this->createMock(Module::class);

        $iconMock = $this->createMock(ModuleIcon::class);
        $iconMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['icon'])
            ->willReturnSelf();
        $moduleMock->expects(self::once())
            ->method('icon')
            ->willReturn($iconMock);

        $stateMock = $this->createMock(ModuleState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['state'])
            ->willReturnSelf();
        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $positionMock = $this->createMock(ModulePosition::class);
        $positionMock->expects(self::once())
            ->method('setValue')
            ->with($dataProvider['position'])
            ->willReturnSelf();
        $moduleMock->expects(self::once())
            ->method('position')
            ->willReturn($positionMock);

        $createdAt = $this->createMock(ModuleCreatedAt::class);
        $createdAt->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($dataProvider['createdAt']))
            ->willReturnSelf();
        $moduleMock->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('setValue')
            ->with(new \DateTime($dataProvider['updatedAt']))
            ->willReturnSelf();
        $moduleMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->factory->expects(self::once())
            ->method('buildModule')
            ->with(
                $idMock,
                $keyMock,
                $nameMock,
                $routeMock,
            )
            ->willReturn($moduleMock);

        $result = $this->factory->buildModuleFromArray($dataObject);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    public function testBuildModuleCreatedAtShouldReturnValueObjectWithDatetime(): void
    {
        $result = $this->factory->buildModuleCreatedAt(new \DateTime());
        $this->assertInstanceOf(ModuleCreatedAt::class, $result);
        $this->assertInstanceOf(\DateTime::class, $result->value());
    }

    public function testBuildModuleUpdatedAtShouldReturnValueObjectWithNull(): void
    {
        $result = $this->factory->buildModuleUpdatedAt();
        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
        $this->assertNull($result->value());
    }

    public function testBuildModuleUpdatedAtShouldReturnValueObjectWithDatetime(): void
    {
        $result = $this->factory->buildModuleUpdatedAt(new \DateTime());
        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
        $this->assertInstanceOf(\DateTime::class, $result->value());
    }

    /**
     * @throws Exception
     */
    public function testBuildModulesShouldReturnModulesObject(): void
    {
        $moduleMock = $this->createMock(Module::class);
        $result = $this->factory->buildModules($moduleMock);

        $this->assertInstanceOf(Modules::class, $result);
    }

    public function testBuildModuleSearchShouldReturnObjectWithNull(): void
    {
        $result = $this->factory->buildModuleSearch();

        $this->assertInstanceOf(ModuleSearch::class, $result);
        $this->assertNull($result->value());
    }

    /**
     * @param array<string, mixed> $dataObject
     *
     * @throws \Exception
     */
    #[DataProviderExternal(DataProviderFactory::class, 'providerModules')]
    public function testBuildModulesFromArrayShouldReturnModules(array $dataObject): void
    {
        $result = $this->factory->buildModulesFromArray($dataObject);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertCount(1, $result->items());
        $this->assertIsArray($result->items());
    }

    public function testBuildModulePositionShouldReturnValueObject(): void
    {
        $result = $this->factory->buildModulePosition();

        $this->assertInstanceOf(ModulePosition::class, $result);
        $this->assertIsInt($result->value());
        $this->assertSame(1, $result->value());
    }

    public function testBuildModuleIconShouldReturnValueObject(): void
    {
        $result = $this->factory->buildModuleIcon();

        $this->assertInstanceOf(ModuleIcon::class, $result);
    }

    /**
     * @throws \Exception
     */
    public function testBuildModuleStateShouldReturnValueObject(): void
    {
        $result = $this->factory->buildModuleState();

        $this->assertInstanceOf(ModuleState::class, $result);
        $this->assertIsInt($result->value());
        $this->assertSame(1, $result->value());
    }

    public function testBuildModulePermissionShouldReturnValueObject(): void
    {
        $result = $this->factory->buildModulePermission('edit');

        $this->assertInstanceOf(ModulePermission::class, $result);
        $this->assertIsString($result->value());
        $this->assertSame('edit', $result->value());
    }
}
