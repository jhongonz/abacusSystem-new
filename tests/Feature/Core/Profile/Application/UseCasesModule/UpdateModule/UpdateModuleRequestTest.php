<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\UpdateModule;

use Core\Profile\Application\UseCasesModule\UpdateModule\UpdateModuleRequest;
use Core\Profile\Domain\ValueObjects\ModuleId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateModuleRequest::class)]
class UpdateModuleRequestTest extends TestCase
{
    private ModuleId|MockObject $moduleId;
    private UpdateModuleRequest $request;

    /** @var array<string, mixed> */
    private array $data = [];

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleId = $this->createMock(ModuleId::class);
        $this->request = new UpdateModuleRequest($this->moduleId, $this->data);
    }

    public function tearDown(): void
    {
        unset($this->moduleId, $this->request, $this->data);
        parent::tearDown();
    }

    public function testModuleIdShouldReturnObject(): void
    {
        $result = $this->request->moduleId();

        $this->assertInstanceOf(ModuleId::class, $result);
        $this->assertSame($result, $this->moduleId);
    }

    public function testDataShouldReturnArray(): void
    {
        $result = $this->request->data();

        $this->assertIsArray($result);
        $this->assertSame($result, $this->data);
    }
}
