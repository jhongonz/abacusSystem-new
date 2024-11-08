<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\CreateProfile;

use Core\Profile\Application\UseCases\CreateProfile\CreateProfile;
use Core\Profile\Application\UseCases\CreateProfile\CreateProfileRequest;
use Core\Profile\Application\UseCases\UpdateProfile\UpdateProfileRequest;
use Core\Profile\Application\UseCases\UseCasesService;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateProfile::class)]
#[CoversClass(UseCasesService::class)]
class CreateProfileTest extends TestCase
{
    private ProfileRepositoryContract|MockObject $repository;
    private CreateProfile $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ProfileRepositoryContract::class);
        $this->useCase = new CreateProfile($this->repository);
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
        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $requestMock = $this->createMock(CreateProfileRequest::class);
        $requestMock->expects(self::once())
            ->method('profile')
            ->willReturn($profileMock);

        $this->repository->expects(self::once())
            ->method('persistProfile')
            ->with($profileMock)
            ->willReturn($profileMock);

        $result = $this->useCase->execute($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
    }

    /**
     * @throws Exception
     */
    public function test_execute_should_return_exception(): void
    {
        $requestMock = $this->createMock(UpdateProfileRequest::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request not valid');

        $this->useCase->execute($requestMock);
    }
}
