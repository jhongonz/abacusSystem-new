<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Management;

use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitution;
use Core\Institution\Application\UseCases\CreateInstitution\CreateInstitutionRequest;
use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitution;
use Core\Institution\Application\UseCases\DeleteInstitution\DeleteInstitutionRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionById;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionByIdRequest;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutions;
use Core\Institution\Application\UseCases\SearchInstitution\SearchInstitutionsRequest;
use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitution;
use Core\Institution\Application\UseCases\UpdateInstitution\UpdateInstitutionRequest;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Infrastructure\Management\InstitutionService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(InstitutionService::class)]
class InstitutionServiceTest extends TestCase
{
    private InstitutionFactoryContract|MockObject $factory;
    private SearchInstitutionById|MockObject $searchInstitutionById;
    private SearchInstitutions|MockObject $searchInstitutions;
    private CreateInstitution|MockObject $createInstitution;
    private UpdateInstitution|MockObject $updateInstitution;
    private DeleteInstitution|MockObject $deleteInstitution;
    private InstitutionService $service;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(InstitutionFactoryContract::class);
        $this->searchInstitutionById = $this->createMock(SearchInstitutionById::class);
        $this->searchInstitutions = $this->createMock(SearchInstitutions::class);
        $this->createInstitution = $this->createMock(CreateInstitution::class);
        $this->updateInstitution = $this->createMock(UpdateInstitution::class);
        $this->deleteInstitution = $this->createMock(DeleteInstitution::class);
        $this->service = new InstitutionService(
            $this->factory,
            $this->searchInstitutionById,
            $this->searchInstitutions,
            $this->updateInstitution,
            $this->createInstitution,
            $this->deleteInstitution
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->service,
            $this->factory,
            $this->searchInstitutions,
            $this->searchInstitutionById,
            $this->deleteInstitution,
            $this->createInstitution,
            $this->updateInstitution,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchInstitutionById_should_return_object(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $request = new SearchInstitutionByIdRequest($idMock);

        $institutionMock = $this->createMock(Institution::class);
        $this->searchInstitutionById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($institutionMock);

        $result = $this->service->searchInstitutionById($idMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_searchInstitutionById_should_return_null(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $request = new SearchInstitutionByIdRequest($idMock);

        $this->searchInstitutionById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->service->searchInstitutionById($idMock);

        $this->assertNotInstanceOf(Institution::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function test_searchInstitutions_should_return_object(): void
    {
        $request = new SearchInstitutionsRequest([]);

        $institutionsMock = $this->createMock(Institutions::class);
        $institutionsMock->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $idMock = $this->createMock(InstitutionId::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionId')
            ->with(1)
            ->willReturn($idMock);

        $institutionMock = $this->createMock(Institution::class);
        $requestSearch = new SearchInstitutionByIdRequest($idMock);
        $this->searchInstitutionById->expects(self::once())
            ->method('execute')
            ->with($requestSearch)
            ->willReturn($institutionMock);

        $institutionsMock->expects(self::once())
            ->method('addItem')
            ->with($institutionMock)
            ->willReturnSelf();

        $this->searchInstitutions->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($institutionsMock);

        $result = $this->service->searchInstitutions();

        $this->assertInstanceOf(Institutions::class, $result);
        $this->assertSame($institutionsMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_updateInstitution_should_return_object(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $request = new UpdateInstitutionRequest($idMock, []);

        $institutionMock = $this->createMock(Institution::class);

        $this->updateInstitution->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($institutionMock);

        $result = $this->service->updateInstitution($idMock, []);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_createInstitution_should_return_object(): void
    {
        $institutionMock = $this->createMock(Institution::class);
        $request = new CreateInstitutionRequest($institutionMock);

        $this->createInstitution->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($institutionMock);

        $result = $this->service->createInstitution($institutionMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_deleteInstitution_should_return_object(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $request = new DeleteInstitutionRequest($idMock);

        $this->deleteInstitution->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $this->service->deleteInstitution($idMock);
        $this->assertTrue(true);
    }
}
