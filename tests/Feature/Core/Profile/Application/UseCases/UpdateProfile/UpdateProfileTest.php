<?php

namespace Tests\Feature\Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\CreateProfile\CreateProfileRequest;
use Core\Profile\Application\UseCases\UpdateProfile\UpdateProfile;
use Core\Profile\Application\UseCases\UpdateProfile\UpdateProfileRequest;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileDescription;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateProfile::class)]
class UpdateProfileTest extends TestCase
{
    private ProfileRepositoryContract|MockObject $repository;
    private UpdateProfile $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(ProfileRepositoryContract::class);
        $this->useCase = new UpdateProfile($this->repository);
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
        $datetime = new \DateTime;
        $data = [
            'state' => 1,
            'description' => 'test',
            'name' => 'test',
            'modules' => [1,2,3],
            'updateAt' => $datetime,
        ];
        $profileId = $this->createMock(ProfileId::class);

        $stateMock = $this->createMock(ProfileState::class);
        $stateMock->expects(self::once())
            ->method('setValue')
            ->with(1)
            ->willReturnSelf();

        $descriptionMock = $this->createMock(ProfileDescription::class);
        $descriptionMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();

        $nameMock = $this->createMock(ProfileName::class);
        $nameMock->expects(self::once())
            ->method('setValue')
            ->with('test')
            ->willReturnSelf();

        $request = $this->createMock(UpdateProfileRequest::class);
        $request->expects(self::once())
            ->method('profileId')
            ->willReturn($profileId);

        $request->expects(self::once())
            ->method('data')
            ->willReturn($data);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('refreshSearch')
            ->willReturnSelf();

        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $profileMock->expects(self::once())
            ->method('description')
            ->willReturn($descriptionMock);

        $profileMock->expects(self::once())
            ->method('name')
            ->willReturn($nameMock);

        $profileMock->expects(self::once())
            ->method('setModulesAggregator')
            ->with([1,2,3])
            ->willReturnSelf();

        $updatedAt = $this->createMock(ProfileUpdatedAt::class);
        $updatedAt->expects(self::once())
            ->method('setValue')
            ->with($datetime)
            ->willReturnSelf();
        $profileMock->expects(self::once())
            ->method('updatedAt')
            ->willReturn($updatedAt);

        $this->repository->expects(self::once())
            ->method('find')
            ->with($profileId)
            ->willReturn($profileMock);

        $this->repository->expects(self::once())
            ->method('persistProfile')
            ->with($profileMock)
            ->willReturn($profileMock);

        $result = $this->useCase->execute($request);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
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
