<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusById;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusByIdRequest;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchCampusById::class)]
class SearchCampusByIdTest extends TestCase
{
    private CampusRepositoryContract|MockObject $campusRepository;
    private SearchCampusById $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusRepository = $this->createMock(CampusRepositoryContract::class);
        $this->useCase = new SearchCampusById($this->campusRepository);
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
    public function test_execute_should_return_object(): void
    {
        $requestMock = $this->createMock(SearchCampusByIdRequest::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $campusMock = $this->createMock(Campus::class);
        $this->campusRepository->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn($campusMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $requestMock = $this->createMock(SearchCampusByIdRequest::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $this->campusRepository->expects(self::once())
            ->method('find')
            ->with($campusIdMock)
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);

        $this->assertNotInstanceOf(Campus::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(CreateCampusRequest::class);

        $this->campusRepository->expects(self::never())
            ->method('find');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
