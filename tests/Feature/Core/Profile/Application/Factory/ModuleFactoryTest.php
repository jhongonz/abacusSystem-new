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
use Core\Profile\Domain\ValueObjects\ModulePosition;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Feature\Core\Profile\Application\Factory\DataProvider\DataProviderFactory;
use Tests\TestCase;

#[CoversClass(ModuleFactory::class)]
class ModuleFactoryTest extends TestCase
{
    private ModuleFactory $factory;

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
     */
    #[DataProviderExternal(DataProviderFactory::class, 'providerModule')]
    public function testBuildModuleFromArrayShouldReturnModuleObject(array $dataObject): void
    {
        $result = $this->factory->buildModuleFromArray($dataObject);
        $data = $dataObject[Module::TYPE];

        $this->assertInstanceOf(Module::class, $result);

        $this->assertSame($data['id'], $result->id()->value());
        $this->assertInstanceOf(ModuleId::class, $result->id());

        $this->assertSame($data['key'], $result->menuKey()->value());
        $this->assertInstanceOf(ModuleMenuKey::class, $result->menuKey());

        $this->assertSame($data['name'], $result->name()->value());
        $this->assertInstanceOf(ModuleName::class, $result->name());

        $this->assertSame($data['route'], $result->route()->value());
        $this->assertInstanceOf(ModuleRoute::class, $result->route());

        $this->assertSame($data['icon'], $result->icon()->value());
        $this->assertInstanceOf(ModuleIcon::class, $result->icon());

        $this->assertSame($data['state'], $result->state()->value());
        $this->assertInstanceOf(ModuleState::class, $result->state());

        $this->assertSame($data['position'], $result->position()->value());
        $this->assertInstanceOf(ModulePosition::class, $result->position());
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
    }
}
