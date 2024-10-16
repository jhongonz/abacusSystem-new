<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\DeleteProfile;

use Core\Profile\Application\UseCases\CreateProfile\CreateProfileRequest;
use Core\Profile\Application\UseCases\DeleteProfile\DeleteProfile;
use Core\Profile\Application\UseCases\DeleteProfile\DeleteProfileRequest;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\ValueObjects\ProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteProfile::class)]
#[CoversClass(UseCasesService::class)]
class DeleteProfileTest extends TestCase
{
    private ProfileRepositoryContract|MockObject $repository;
    private DeleteProfile $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ProfileRepositoryContract::class);
        $this->useCase = new DeleteProfile($this->repository);
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
    public function test_execute_should_return_null(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);

        $requestMock = $this->createMock(DeleteProfileRequest::class);
        $requestMock->expects(self::once())
            ->method('id')
            ->willReturn($profileIdMock);

        $this->repository->expects(self::once())
            ->method('deleteProfile')
            ->with($profileIdMock);

        $result = $this->useCase->execute($requestMock);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $request = $this->createMock(CreateProfileRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($request);
    }
}
