<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\DeleteModule;

use Core\Profile\Application\UseCasesModule\DeleteModule\DeleteModuleRequest;
use Core\Profile\Domain\ValueObjects\ModuleId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteModuleRequest::class)]
class DeleteModuleRequestTest extends TestCase
{
    private ModuleId|MockObject $moduleId;
    private DeleteModuleRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleId = $this->createMock(ModuleId::class);
        $this->request = new DeleteModuleRequest($this->moduleId);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->moduleId
        );
        parent::tearDown();
    }

    public function testModuleIdShouldReturnObject(): void
    {
        $result = $this->request->id();

        $this->assertInstanceOf(ModuleId::class, $result);
        $this->assertSame($result, $this->moduleId);
    }
}
