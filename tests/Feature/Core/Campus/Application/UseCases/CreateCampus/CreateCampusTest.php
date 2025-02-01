<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\CreateCampus;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampus;
use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampusRequest;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateCampus::class)]
#[CoversClass(UseCasesService::class)]
class CreateCampusTest extends TestCase
{
    private CampusRepositoryContract|MockObject $campusRepository;
    private CreateCampus $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusRepository = $this->createMock(CampusRepositoryContract::class);
        $this->useCase = new CreateCampus($this->campusRepository);
    }

    public function tearDown(): void
    {
        unset(
            $this->campusRepository,
            $this->useCase
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnObject(): void
    {
        $requestMock = $this->createMock(CreateCampusRequest::class);

        $campusMock = $this->createMock(Campus::class);
        $campusMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();
        $requestMock->expects(self::once())
            ->method('campus')
            ->willReturn($campusMock);

        $this->campusRepository->expects(self::once())
            ->method('persistCampus')
            ->with($campusMock)
            ->willReturn($campusMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteShouldReturnException(): void
    {
        $requestMock = $this->createMock(UpdateCampusRequest::class);

        $this->campusRepository->expects(self::never())
            ->method('persistCampus');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
