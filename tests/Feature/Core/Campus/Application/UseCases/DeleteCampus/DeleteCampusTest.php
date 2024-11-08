<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\DeleteCampus;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampus;
use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampusRequest;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteCampus::class)]
#[CoversClass(UseCasesService::class)]
class DeleteCampusTest extends TestCase
{
    private CampusRepositoryContract|MockObject $campusRepository;
    private DeleteCampus $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusRepository = $this->createMock(CampusRepositoryContract::class);
        $this->useCase = new DeleteCampus($this->campusRepository);
    }

    public function tearDown(): void
    {
        unset(
            $this->useCase,
            $this->campusRepository
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_delete_and_return_void(): void
    {
        $requestMock = $this->createMock(DeleteCampusRequest::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $this->campusRepository->expects(self::once())
            ->method('delete')
            ->with($campusIdMock);

        $result = $this->useCase->execute($requestMock);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(CreateCampusRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
