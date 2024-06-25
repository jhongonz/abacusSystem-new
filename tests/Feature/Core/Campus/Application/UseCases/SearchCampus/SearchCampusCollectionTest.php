<?php

namespace Tests\Feature\Core\Campus\Application\UseCases\SearchCampus;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollection;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollectionRequest;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchCampusCollection::class)]
class SearchCampusCollectionTest extends TestCase
{
    private CampusRepositoryContract|MockObject $campusRepository;
    private SearchCampusCollection $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->campusRepository = $this->createMock(CampusRepositoryContract::class);
        $this->useCase = new SearchCampusCollection($this->campusRepository);
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
        $requestMock = $this->createMock(SearchCampusCollectionRequest::class);

        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $requestMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $requestMock->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $campusCollectionMock = $this->createMock(CampusCollection::class);
        $this->campusRepository->expects(self::once())
            ->method('getAll')
            ->with($institutionIdMock, [])
            ->willReturn($campusCollectionMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(CampusCollection::class, $result);
        $this->assertSame($campusCollectionMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $requestMock = $this->createMock(SearchCampusCollectionRequest::class);

        $institutionIdMock = $this->createMock(CampusInstitutionId::class);
        $requestMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionIdMock);

        $requestMock->expects(self::once())
            ->method('filters')
            ->willReturn([]);

        $this->campusRepository->expects(self::once())
            ->method('getAll')
            ->with($institutionIdMock, [])
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);

        $this->assertNotInstanceOf(SearchCampusCollection::class, $result);
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
            ->method('getAll');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
