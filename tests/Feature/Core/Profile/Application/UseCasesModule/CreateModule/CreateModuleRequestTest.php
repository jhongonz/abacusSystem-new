<?php

namespace Tests\Feature\Core\Profile\Application\UseCasesModule\CreateModule;

use Core\Profile\Application\UseCasesModule\CreateModule\CreateModuleRequest;
use Core\Profile\Domain\Module;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateModuleRequest::class)]
class CreateModuleRequestTest extends TestCase
{
    private Module|MockObject $module;
    private CreateModuleRequest $request;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->module = $this->createMock(Module::class);
        $this->request = new CreateModuleRequest($this->module);
    }

    public function tearDown(): void
    {
        unset(
            $this->request,
            $this->module
        );
        parent::tearDown();
    }

    public function test_module_should_return_object(): void
    {
        $result = $this->request->module();

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($result, $this->module);
    }
}
