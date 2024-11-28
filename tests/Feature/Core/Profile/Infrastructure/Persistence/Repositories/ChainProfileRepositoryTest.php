<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-17 21:52:32
 */

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Core\Profile\Domain\ValueObjects\ProfileName;
use Core\Profile\Exceptions\ProfileNotFoundException;
use Core\Profile\Exceptions\ProfilesNotFoundException;
use Core\Profile\Infrastructure\Persistence\Repositories\ChainProfileRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainProfileRepository::class)]
class ChainProfileRepositoryTest extends TestCase
{
    private ChainProfileRepository|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ChainProfileRepository::class)
            ->onlyMethods(['read', 'readFromRepositories', 'write'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testFunctionNamePersistShouldReturnString(): void
    {
        $result = $this->repository->functionNamePersist();

        $this->assertIsString($result);
        $this->assertEquals('persistProfile', $result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnValueObject(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $profileMock = $this->createMock(Profile::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $profileIdMock)
            ->willReturn($profileMock);

        $result = $this->repository->find($profileIdMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnNull(): void
    {
        $profileId = $this->createMock(ProfileId::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $profileId)
            ->willReturn(null);

        $result = $this->repository->find($profileId);

        $this->assertNotInstanceOf(Profile::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindShouldReturnException(): void
    {
        $profileIdMock = $this->createMock(ProfileId::class);
        $profileIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('find', $profileIdMock)
            ->willThrowException(new \Exception());

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found by id 1');

        $this->repository->find($profileIdMock);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindCriteriaShouldReturnValueObject(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileMock = $this->createMock(Profile::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $profileName)
            ->willReturn($profileMock);

        $result = $this->repository->findCriteria($profileName);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindCriteriaShouldReturnNull(): void
    {
        $profileName = $this->createMock(ProfileName::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $profileName)
            ->willReturn(null);

        $result = $this->repository->findCriteria($profileName);

        $this->assertNotInstanceOf(Profile::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws ProfileNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testFindCriteriaShouldReturnException(): void
    {
        $profileName = $this->createMock(ProfileName::class);
        $profileName->expects(self::once())
            ->method('value')
            ->willReturn('test');

        $this->repository->expects(self::once())
            ->method('read')
            ->with('findCriteria', $profileName)
            ->willThrowException(new \Exception());

        $this->expectException(ProfileNotFoundException::class);
        $this->expectExceptionMessage('Profile not found by name test');

        $this->repository->findCriteria($profileName);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $profileId = $this->createMock(ProfileId::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('deleteProfile', $profileId);

        $this->repository->deleteProfile($profileId);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Exception
     */
    public function testPersistEmployeeShouldReturnVoid(): void
    {
        $profileMock = $this->createMock(Profile::class);

        $this->repository->expects(self::once())
            ->method('write')
            ->with('persistProfile', $profileMock)
            ->willReturn($profileMock);

        $result = $this->repository->persistProfile($profileMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Throwable
     */
    public function testGetAllShouldReturnCollection(): void
    {
        $profilesMock = $this->createMock(Profiles::class);

        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn($profilesMock);

        $result = $this->repository->getAll();

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($profilesMock, $result);
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws \Throwable
     */
    public function testGetAllShouldReturnNull(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willReturn(null);

        $result = $this->repository->getAll();

        $this->assertNotInstanceOf(Profiles::class, $result);
        $this->assertNull($result);
    }

    /**
     * @throws ProfilesNotFoundException
     * @throws \Throwable
     */
    public function testGetAllShouldReturnException(): void
    {
        $this->repository->expects(self::once())
            ->method('read')
            ->with('getAll', [])
            ->willThrowException(new \Exception());

        $this->expectException(ProfilesNotFoundException::class);
        $this->expectExceptionMessage('Profiles not found');

        $this->repository->getAll();
    }
}
