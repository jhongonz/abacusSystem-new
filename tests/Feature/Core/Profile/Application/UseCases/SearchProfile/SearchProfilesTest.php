<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\SearchProfile\SearchProfileByIdRequest;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfiles;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfilesRequest;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchProfiles::class)]
class SearchProfilesTest extends TestCase
{
    private ProfileRepositoryContract|MockObject $repository;
    private SearchProfiles $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ProfileRepositoryContract::class);
        $this->useCase = new SearchProfiles($this->repository);
    }

    public function tearDown(): void
    {
        unset(
            $this->useCase,
            $this->repository
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_object(): void
    {
        $requestMock = $this->createMock(SearchProfilesRequest::class);
        $requestMock->expects(self::once())
            ->method('filters')
            ->willReturn(['testing']);

        $profilesMock = $this->createMock(Profiles::class);
        $this->repository->expects(self::once())
            ->method('getAll')
            ->with(['testing'])
            ->willReturn($profilesMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($result, $profilesMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $requestMock = $this->createMock(SearchProfilesRequest::class);
        $requestMock->expects(self::once())
            ->method('filters')
            ->willReturn(['testing']);

        $this->repository->expects(self::once())
            ->method('getAll')
            ->with(['testing'])
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $request = $this->createMock(SearchProfileByIdRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($request);
    }
}
