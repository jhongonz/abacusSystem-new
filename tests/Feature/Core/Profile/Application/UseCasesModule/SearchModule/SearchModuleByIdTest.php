<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleById;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleByIdRequest;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchModuleById::class)]
class SearchModuleByIdTest extends TestCase
{
    private ModuleRepositoryContract|MockObject $repository;
    private SearchModuleById $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ModuleRepositoryContract::class);
        $this->useCase = new SearchModuleById($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_object(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $requestMock = $this->createMock(SearchModuleByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('moduleId')
            ->willReturn($moduleId);

        $moduleMock = $this->createMock(Module::class);
        $this->repository->expects(self::once())
            ->method('find')
            ->with($moduleId)
            ->willReturn($moduleMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $moduleId = $this->createMock(ModuleId::class);
        $requestMock = $this->createMock(SearchModuleByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('moduleId')
            ->willReturn($moduleId);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($moduleId)
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);
        $this->assertNull($result);
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
