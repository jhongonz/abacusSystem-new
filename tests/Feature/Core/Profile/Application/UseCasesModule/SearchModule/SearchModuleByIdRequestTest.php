<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\SearchModule;

use Core\Profile\Application\UseCasesModule\SearchModule\SearchModuleByIdRequest;
use Core\Profile\Domain\ValueObjects\ModuleId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchModuleByIdRequest::class)]
class SearchModuleByIdRequestTest extends TestCase
{
    private ModuleId|MockObject $moduleId;
    private SearchModuleByIdRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleId = $this->createMock(ModuleId::class);
        $this->request = new SearchModuleByIdRequest($this->moduleId);
    }

    public function tearDown(): void
    {
        unset($this->request, $this->moduleId);
        parent::tearDown();
    }

    public function test_moduleId_should_return_value_object(): void
    {
        $result = $this->request->moduleId();

        $this->assertInstanceOf(ModuleId::class, $result);
        $this->assertSame($result, $this->moduleId);
    }
}
