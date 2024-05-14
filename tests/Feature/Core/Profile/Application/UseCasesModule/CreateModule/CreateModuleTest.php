<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\CreateModule;

use Core\Profile\Application\UseCasesModule\CreateModule\CreateModule;
use Core\Profile\Application\UseCasesModule\CreateModule\CreateModuleRequest;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateModule::class)]
class CreateModuleTest extends TestCase
{
    private ModuleRepositoryContract|MockObject $repository;
    private CreateModule $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ModuleRepositoryContract::class);
        $this->useCase = new CreateModule($this->repository);
    }

    public function tearDown(): void
    {
        unset(
            $this->repository,
            $this->useCase
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_object(): void
    {
        $moduleMock = $this->createMock(Module::class);
        $moduleMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $requestMock = $this->createMock(CreateModuleRequest::class);
        $requestMock->expects(self::once())
            ->method('module')
            ->willReturn($moduleMock);

        $this->repository->expects(self::once())
            ->method('persistModule')
            ->with($moduleMock)
            ->willReturn($moduleMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $moduleMock);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(DeleteModuleRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
