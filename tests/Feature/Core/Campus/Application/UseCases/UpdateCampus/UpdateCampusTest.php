<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\UpdateCampus;

use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampus;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampusRequest;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Feature\Core\Campus\Application\DataProvider\UpdateCampusDataProvider;
use Tests\TestCase;

#[CoversClass(UpdateCampus::class)]
#[CoversClass(UseCasesService::class)]
class UpdateCampusTest extends TestCase
{
    private CampusRepositoryContract|MockObject $campusRepository;
    private UpdateCampus $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusRepository = $this->createMock(CampusRepositoryContract::class);
        $this->useCase = new UpdateCampus($this->campusRepository);
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
     * @param array<string|int, mixed> $dataTest
     *
     * @throws Exception
     * @throws \Exception
     */
    #[DataProviderExternal(UpdateCampusDataProvider::class, 'provider_update_campus')]
    public function testExecuteShouldReturnObject(array $dataTest): void
    {
        $requestMock = $this->createMock(UpdateCampusRequest::class);

        $campusId = $this->createMock(CampusId::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($campusId);

        $requestMock->expects(self::once())
            ->method('data')
            ->willReturn($dataTest);

        $campusMock = $this->createMock(Campus::class);
        $campusMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $this->campusRepository->expects(self::once())
            ->method('find')
            ->with($campusId)
            ->willReturn($campusMock);

        $this->campusRepository->expects(self::once())
            ->method('persistCampus')
            ->with($campusMock)
            ->willReturn($campusMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }
}
