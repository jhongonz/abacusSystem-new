<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\SearchProfile;

use Core\Profile\Application\UseCases\DeleteProfile\DeleteProfileRequest;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfileById;
use Core\Profile\Application\UseCases\SearchProfile\SearchProfileByIdRequest;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(SearchProfileById::class)]
class SearchProfileByIdTest extends TestCase
{
    private ProfileRepositoryContract|MockObject $repository;
    private SearchProfileById $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ProfileRepositoryContract::class);
        $this->useCase = new SearchProfileById($this->repository);
    }

    public function tearDown(): void
    {
        unset(
            $this->repository,
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
        $profileIdMock = $this->createMock(ProfileId::class);

        $requestMock = $this->createMock(SearchProfileByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileIdMock);

        $profileMock = $this->createMock(Profile::class);
        $this->repository->expects(self::once())
            ->method('find')
            ->with($profileIdMock)
            ->willReturn($profileMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($result, $profileMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_execute_should_return_null(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);

        $requestMock = $this->createMock(SearchProfileByIdRequest::class);
        $requestMock->expects(self::once())
            ->method('profileId')
            ->willReturn($profileIdMock);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($profileIdMock)
            ->willReturn(null);

        $result = $this->useCase->execute($requestMock);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $request = $this->createMock(DeleteProfileRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($request);
    }
}
