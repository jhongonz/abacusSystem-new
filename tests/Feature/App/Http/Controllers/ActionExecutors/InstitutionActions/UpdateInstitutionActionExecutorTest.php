<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\InstitutionActions;

use App\Http\Controllers\ActionExecutors\InstitutionActions\UpdateInstitutionActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateInstitutionActionExecutor::class)]
class UpdateInstitutionActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private ImageManagerInterface|MockObject $imageManager;
    private UpdateInstitutionActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->actionExecutor = new UpdateInstitutionActionExecutor(
            $this->orchestratorHandler,
            $this->imageManager
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestratorHandler,
            $this->imageManager,
            $this->actionExecutor
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_invoke_should_return_institution(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(8))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'code',
                'name',
                'shortname',
                'phone',
                'email',
                'address',
                'observations',
                'token'
            );

        $requestMock->expects(self::once())
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('/var/www/abacusSystem-new/public/images/tmp/token.jpg')
            ->willReturn($imageMock);

        $institutionMock = $this->createMock(Institution::class);
        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('update-institution', $requestMock)
            ->willReturn($institutionMock);

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('update-institution-action', $result);
    }
}
