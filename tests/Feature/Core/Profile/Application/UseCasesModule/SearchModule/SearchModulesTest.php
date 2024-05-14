<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModules;
use Core\Profile\Application\UseCasesModule\SearchModule\SearchModulesRequest;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Core\Profile\Domain\Modules;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchModules::class)]
class SearchModulesTest extends TestCase
{
    private ModuleRepositoryContract|MockObject $repository;
    private SearchModules $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ModuleRepositoryContract::class);
        $this->useCase = new SearchModules($this->repository);
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
        $request = $this->createMock(SearchModulesRequest::class);
        $request->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $modulesMock = $this->createMock(Modules::class);
        $this->repository->expects(self::once())
            ->method('getAll')
            ->with([])
            ->willReturn($modulesMock);

        $result = $this->useCase->execute($request);

        $this->assertInstanceOf(Modules::class, $result);
        $this->assertSame($result, $modulesMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $request = $this->createMock(SearchModulesRequest::class);
        $request->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $this->repository->expects(self::once())
            ->method('getAll')
            ->with([])
            ->willReturn(null);

        $result = $this->useCase->execute($request);
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
