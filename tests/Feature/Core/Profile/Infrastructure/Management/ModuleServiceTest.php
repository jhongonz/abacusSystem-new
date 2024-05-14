<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Management;

use Core\Profile\Application\UseCasesModule\CreateModule\CreateModule;
use Core\Profile\Application\UseCasesModule\CreateModule\CreateModuleRequest;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModule;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleById;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleByIdRequest;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModules;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModulesRequest;
use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModule;
use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModuleRequest;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Infrastructure\Management\ModuleService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ModuleService::class)]
class ModuleServiceTest extends TestCase
{
    private ModuleFactoryContract|MockObject $factory;
    private SearchModuleById|MockObject $searchModuleById;
    private SearchModules|MockObject $searchModules;
    private UpdateModule|MockObject $updateModule;
    private DeleteModule|MockObject $deleteModule;
    private CreateModule|MockObject $createModule;
    private ModuleService $service;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(ModuleFactoryContract::class);
        $this->searchModuleById = $this->createMock(SearchModuleById::class);
        $this->searchModules = $this->createMock(SearchModules::class);
        $this->updateModule = $this->createMock(UpdateModule::class);
        $this->deleteModule = $this->createMock(DeleteModule::class);
        $this->createModule = $this->createMock(CreateModule::class);
        $this->service = new ModuleService(
            $this->factory,
            $this->searchModuleById,
            $this->searchModules,
            $this->updateModule,
            $this->deleteModule,
            $this->createModule
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->factory,
            $this->service,
            $this->searchModules,
            $this->searchModuleById,
            $this->updateModule,
            $this->deleteModule,
            $this->createModule
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchModuleById_should_return_object(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $request = new SearchModuleByIdRequest($moduleId);

        $moduleMock = $this->createMock(Module::class);
        $this->searchModuleById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($moduleMock);

        $result = $this->service->searchModuleById($moduleId);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchModuleById_should_return_null(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $request = new SearchModuleByIdRequest($moduleId);

        $this->searchModuleById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->service->searchModuleById($moduleId);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchModules_should_return_object(): void
    {
        $filters = [];
        $request = new SearchModulesRequest($filters);

        $modulesMock = $this->createMock(Modules::class);
        $modulesMock->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->searchModules->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($modulesMock);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $this->factory->expects(self::once())
            ->method('buildModuleId')
            ->with(1)
            ->willReturn($moduleIdMock);

        $moduleMock = $this->createMock(Module::class);
        $this->searchModuleById->expects(self::once())
            ->method('execute')
            ->willReturn($moduleMock);

        $modulesMock->expects(self::once())
            ->method('addItem')
            ->with($moduleMock)
            ->willReturnSelf();

        $result = $this->service->searchModules($filters);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($modulesMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_updateModule_should_return_void(): void
    {
        $data = [];
        $moduleId = $this->createMock(ModuleId::class);
        $request = new UpdateModuleRequest($moduleId, $data);

        $moduleMock = $this->createMock(Module::class);
        $this->updateModule->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($moduleMock);

        $this->service->updateModule($moduleId, $data);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_deleteModule_should_return_void(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $request = new DeleteModuleRequest($moduleId);

        $this->deleteModule->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $this->service->deleteModule($moduleId);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_createModule_should_return_object(): void
    {
        $moduleMock = $this->createMock(Module::class);
        $request = new CreateModuleRequest($moduleMock);

        $this->createModule->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($moduleMock);

        $result = $this->service->createModule($moduleMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }
}
