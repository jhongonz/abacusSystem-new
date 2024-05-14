<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\DeleteModule;

use Core\Profile\Application\UseCasesModule\CreateModule\CreateModuleRequest;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModule;
use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\ValueObjects\ModuleId;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class DeleteModuleTest extends TestCase
{
    private ModuleRepositoryContract|MockObject $repository;
    private DeleteModule $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ModuleRepositoryContract::class);
        $this->useCase = new DeleteModule($this->repository);
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
    public function test_execute_should_return_null(): void
    {
        $moduleId = $this->createMock(ModuleId::class);

        $request = $this->createMock(DeleteModuleRequest::class);
        $request->expects(self::once())
            ->method('id')
            ->willReturn($moduleId);

        $this->repository->expects(self::once())
            ->method('deleteModule')
            ->with($moduleId);

        $result = $this->useCase->execute($request);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $request = $this->createMock(CreateModuleRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($request);
    }
}
