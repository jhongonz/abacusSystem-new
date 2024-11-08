<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\UpdateModule;

use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModule;
use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModuleRequest;
use Core\Profile\Application\UseCasesModule\UseCasesService;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleIcon;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use Core\Profile\Domain\ValueObjects\ModuleName;
use Core\Profile\Domain\ValueObjects\ModulePosition;
use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateModule::class)]
#[CoversClass(UseCasesService::class)]
class UpdateModuleTest extends TestCase
{
    private ModuleRepositoryContract|MockObject $repository;
    private UpdateModule $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ModuleRepositoryContract::class);
        $this->useCase = new UpdateModule($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->useCase, $this->repository);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_object(): void
    {
        $datetime = new \DateTime;

        $dataUpdate = [
            'name' => 'test',
            'route' => 'test',
            'icon' => 'test',
            'key' => 'test',
            'position' => 1,
            'state' => 2,
            'updatedAt' => $datetime,
        ];
        $moduleId = $this->createMock(ModuleId::class);

        $requestMock = $this->createMock(UpdateModuleRequest::class);
        $requestMock->expects(self::once())
            ->method('data')
            ->willReturn($dataUpdate);

        $requestMock->expects(self::once())
            ->method('moduleId')
            ->willReturn($moduleId);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $nameMock = $this->createMock(ModuleName::class);
        $nameMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();

        $moduleMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $routeMock = $this->createMock(ModuleRoute::class);
        $routeMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();

        $moduleMock->expects(self::once())
            ->method('route')
            ->willReturn($routeMock);

        $iconMock = $this->createMock(ModuleIcon::class);
        $iconMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();

        $moduleMock->expects(self::once())
            ->method('icon')
            ->willReturn($iconMock);

        $keyMock = $this->createMock(ModuleMenuKey::class);
        $keyMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();

        $moduleMock->expects(self::once())
            ->method('menuKey')
            ->willReturn($keyMock);

        $positionMock = $this->createMock(ModulePosition::class);
        $positionMock->expects(self::once())
            ->method('setValue')
            ->with(1)
            ->willReturnSelf();

        $moduleMock->expects(self::once())
            ->method('position')
            ->willReturn($positionMock);

        $stateMock = $this->createMock(ModuleState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with(2)
            ->willReturnSelf();

        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $updateAtMock = $this->createMock(ModuleUpdatedAt::class);
        $updateAtMock->expects(self::once())
            ->method('setValue')
            ->with($datetime)
            ->willReturnSelf();
        $moduleMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updateAtMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($moduleId)
            ->willReturn($moduleMock);

        $this->repository->expects(self::once())
            ->method('persistModule')
            ->with($moduleMock)
            ->willReturn($moduleMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $request = $this->createMock(DeleteModuleRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($request);
    }
}
