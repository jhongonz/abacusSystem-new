<?php

namespace Tests\Feature\Core\Campus\Infrastructure\Persistence\Repositories;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Exceptions\CampusNotFoundException;
use Core\Campus\Exceptions\CampusPersistException;
use Core\Campus\Infrastructure\Persistence\Repositories\RedisCampusRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(RedisCampusRepository::class)]
class RedisCampusRepositoryTest extends TestCase
{
    private RedisCampusRepository $repository;
    private CampusFactoryContract|MockObject $campusFactoryMock;
    private CampusDataTransformerContract|MockObject $campusDataTransformerMock;
    private LoggerInterface|MockObject $loggerMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusFactoryMock = $this->createMock(CampusFactoryContract::class);
        $this->campusDataTransformerMock = $this->createMock(CampusDataTransformerContract::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->repository = new RedisCampusRepository(
            $this->campusFactoryMock,
            $this->campusDataTransformerMock,
            $this->loggerMock
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->repository,
            $this->campusFactoryMock,
            $this->campusDataTransformerMock,
            $this->loggerMock
        );
        parent::tearDown();
    }

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(100, $result);
    }

    public function testChangePriorityShouldChangeValueAndReturnSelf(): void
    {
        $result = $this->repository->changePriority(150);
        $value = $result->priority();

        $this->assertInstanceOf(RedisCampusRepository::class, $result);
        $this->assertSame($result, $this->repository);
        $this->assertIsInt($value);
        $this->assertSame(150, $value);
    }

    /**
     * @throws Exception
     * @throws CampusNotFoundException
     */
    public function testFindShouldReturnUserObject(): void
    {
        $key = 'campus::1';

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn('{}');

        $campusMock = $this->createMock(Campus::class);
        $this->campusFactoryMock->expects(self::once())
            ->method('buildCampusFromArray')
            ->with([])
            ->willReturn($campusMock);

        $result = $this->repository->find($campusIdMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws Exception
     * @throws CampusNotFoundException
     */
    public function testFindShouldReturnUserNull(): void
    {
        $key = 'campus::1';

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn(null);

        $this->campusFactoryMock->expects(self::never())
            ->method('buildCampusFromArray');

        $result = $this->repository->find($campusIdMock);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws CampusNotFoundException
     */
    public function testFindShouldReturnException(): void
    {
        $key = 'campus::1';

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with($key)
            ->andThrow(
                CampusNotFoundException::class,
                'Campus not found by id 1'
            );

        $this->campusFactoryMock->expects(self::never())
            ->method('buildCampusFromArray');

        $this->expectException(CampusNotFoundException::class);
        $this->expectExceptionMessage('Campus not found by id 1');

        $this->repository->find($campusIdMock);
    }

    /**
     * @throws Exception
     */
    public function testGetAllShouldReturnNull(): void
    {
        $institutionIdMock = $this->createMock(CampusInstitutionId::class);

        $result = $this->repository->getAll($institutionIdMock);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testDeleteShouldDeleteObject(): void
    {
        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('delete')
            ->once()
            ->with('campus::1')
            ->andReturnUndefined();

        $this->repository->delete($campusIdMock);
    }

    /**
     * @throws CampusPersistException
     * @throws Exception
     */
    public function testPersistCampusShouldReturnValueObject(): void
    {
        $campusMock = $this->createMock(Campus::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $campusMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $this->campusDataTransformerMock->expects(self::once())
            ->method('write')
            ->with($campusMock)
            ->willReturnSelf();

        $this->campusDataTransformerMock->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with(
                'campus::1',
                json_encode([])
            )
            ->andReturnUndefined();

        $result = $this->repository->persistCampus($campusMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @throws CampusPersistException
     * @throws Exception
     */
    public function testPersistCampusShouldReturnException(): void
    {
        $campusMock = $this->createMock(Campus::class);

        $campusIdMock = $this->createMock(CampusId::class);
        $campusIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $campusMock->expects(self::once())
            ->method('id')
            ->willReturn($campusIdMock);

        $this->campusDataTransformerMock->expects(self::once())
            ->method('write')
            ->with($campusMock)
            ->willReturnSelf();

        $this->campusDataTransformerMock->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with(
                'campus::1',
                json_encode([])
            )
            ->andThrow(\Exception::class, 'Error persisting data');

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('Error persisting data');

        $this->expectException(CampusPersistException::class);
        $this->expectExceptionMessage('It could not persist Campus with key campus::1 in redis');

        $result = $this->repository->persistCampus($campusMock);

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }
}
