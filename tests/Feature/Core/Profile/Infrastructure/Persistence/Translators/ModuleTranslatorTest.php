<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Translators;

use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModulePosition;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Module as ModuleModel;
use Core\Profile\Infrastructure\Persistence\Translators\ModuleTranslator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ModuleTranslator::class)]
class ModuleTranslatorTest extends TestCase
{
    private ModuleModel|MockObject $model;
    private ModuleFactoryContract|MockObject $factory;
    private ModuleTranslator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(ModuleFactoryContract::class);
        $this->translator = new ModuleTranslator($this->factory);
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->factory,
            $this->translator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testSetModelShouldReturnSelf(): void
    {
        $model = $this->createMock(ModuleModel::class);
        $result = $this->translator->setModel($model);

        $this->assertInstanceOf(ModuleTranslator::class, $result);
        $this->assertSame($this->translator, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testToDomainShouldReturnObject(): void
    {
        $this->model = $this->createMock(ModuleModel::class);
        $moduleMock = $this->createMock(Module::class);

        $this->model->expects(self::once())
            ->method('id')
            ->willReturn(1);
        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $this->model->expects(self::once())
            ->method('menuKey')
            ->willReturn('test');
        $menuKey = $this->createMock(ModuleMenuKey::class);
        $this->factory->expects(self::once())
            ->method('buildModuleMenuKey')
            ->with('test')
            ->willReturn($menuKey);

        $this->model->expects(self::once())
            ->method('name')
            ->willReturn('test');
        $name = $this->createMock(ModuleName::class);
        $this->factory->expects(self::once())
            ->method('buildModuleName')
            ->with('test')
            ->willReturn($name);

        $this->model->expects(self::once())
            ->method('route')
            ->willReturn('test');
        $route = $this->createMock(ModuleRoute::class);
        $this->factory->expects(self::once())
            ->method('buildModuleRoute')
            ->with('test')
            ->willReturn($route);

        $this->model->expects(self::once())
            ->method('icon')
            ->willReturn('test');
        $icon = $this->createMock(ModuleIcon::class);
        $this->factory->expects(self::once())
            ->method('buildModuleIcon')
            ->with('test')
            ->willReturn($icon);

        $datetime = new \DateTime('2024-05-14 12:30');
        $this->model->expects(self::once())
            ->method('createdAt')
            ->willReturn($datetime);
        $updatedAt = $this->createMock(ModuleCreatedAt::class);
        $this->factory->expects(self::once())
            ->method('buildModuleCreatedAt')
            ->with($datetime)
            ->willReturn($updatedAt);

        $this->model->expects(self::once())
            ->method('position')
            ->willReturn(2);
        $position = $this->createMock(ModulePosition::class);
        $this->factory->expects(self::once())
            ->method('buildModulePosition')
            ->with(2)
            ->willReturn($position);

        $this->model->expects(self::once())
            ->method('search')
            ->willReturn('test');
        $search = $this->createMock(ModuleSearch::class);
        $this->factory->expects(self::once())
            ->method('buildModuleSearch')
            ->with('test')
            ->willReturn($search);

        $this->model->expects(self::once())
            ->method('updatedAt')
            ->willReturn($datetime);
        $updatedAt = $this->createMock(ModuleUpdatedAt::class);
        $this->factory->expects(self::once())
            ->method('buildModuleUpdatedAt')
            ->with($datetime)
            ->willReturn($updatedAt);

        $this->factory->expects(self::once())
            ->method('buildModule')
            ->willReturn($moduleMock);

        $this->translator->setModel($this->model);
        $result = $this->translator->toDomain();

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    public function testSetCollectionShouldReturnSelf(): void
    {
        $result = $this->translator->setCollection([1]);

        $this->assertInstanceOf(ModuleTranslator::class, $result);
        $this->assertSame($this->translator, $result);
    }

    public function testToDomainCollectionShouldReturnObject(): void
    {
        $this->translator->setCollection([1]);
        $result = $this->translator->toDomainCollection();

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertIsArray($result->aggregator());
        $this->assertSame([1], $result->aggregator());
        $this->assertCount(1, $result->aggregator());
    }
}
