<?php

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Core\Institution\Exceptions\InstitutionPersistException;
use Core\Institution\Exceptions\InstitutionsNotFoundException;
use Core\Institution\Infrastructure\Persistence\Repositories\RedisInstitutionRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(RedisInstitutionRepository::class)]
class RedisInstitutionRepositoryTest extends TestCase
{
    private InstitutionFactoryContract|MockObject $factory;
    private InstitutionDataTransformerContract|MockObject $dataTransformer;
    private LoggerInterface|MockObject $logger;
    private RedisInstitutionRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(InstitutionFactoryContract::class);
        $this->dataTransformer = $this->createMock(InstitutionDataTransformerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->repository = new RedisInstitutionRepository(
            $this->factory,
            $this->dataTransformer,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->factory,
            $this->dataTransformer,
            $this->logger,
            $this->repository
        );
        parent::tearDown();
    }

    public function testPriorityShouldReturnInt(): void
    {
        $result = $this->repository->priority();

        $this->assertSame(100, $result);
        $this->assertIsInt($result);
    }

    public function testChangePriorityShouldReturnSelf(): void
    {
        $result = $this->repository->changePriority(50);

        $this->assertInstanceOf(RedisInstitutionRepository::class, $result);
        $this->assertSame($result, $this->repository);
        $this->assertSame(50, $result->priority());
    }

    /**
     * @throws Exception
     * @throws InstitutionsNotFoundException
     */
    public function testFindShouldReturnObject(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $dataExpected[Institution::TYPE] = [
            'createdAt' => new \DateTime('2024-06-03 08:50:00'),
            'updatedAt' => new \DateTime('2024-06-03 08:50:00'),
        ];
        Redis::shouldReceive('get')
            ->once()
            ->with('institution::1')
            ->andReturn(json_encode($dataExpected));

        $institution = $this->createMock(Institution::class);
        $this->factory->expects(self::once())
            ->method('buildInstitutionFromArray')
            ->withAnyParameters()
            ->willReturn($institution);

        $result = $this->repository->find($idMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institution, $result);
    }

    /**
     * @throws Exception
     * @throws InstitutionsNotFoundException
     */
    public function testFindShouldReturnNull(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('institution::1')
            ->andReturn(null);

        $this->factory->expects(self::never())
            ->method('buildInstitutionFromArray');

        $result = $this->repository->find($idMock);
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws InstitutionsNotFoundException
     */
    public function testFindShouldReturnException(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('get')
            ->once()
            ->with('institution::1')
            ->andThrow(new \Exception('testing'));

        $this->factory->expects(self::never())
            ->method('buildInstitutionFromArray');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(InstitutionsNotFoundException::class);
        $this->expectExceptionMessage('Institution not found by id 1');

        $this->repository->find($idMock);
    }

    public function testGetAllShouldReturnNull(): void
    {
        $result = $this->repository->getAll();
        $this->assertNull($result);
    }

    /**
     * @throws Exception
     */
    public function testDeleteShouldReturnVoid(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        Redis::shouldReceive('delete')
            ->once()
            ->with('institution::1')
            ->andReturnUndefined();

        $this->repository->delete($idMock);
    }

    /**
     * @throws Exception
     * @throws InstitutionPersistException
     */
    public function testPersistInstitutionShouldReturnObject(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $institutionMock = $this->createMock(Institution::class);
        $institutionMock->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($institutionMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('institution::2', '[]')
            ->andReturnUndefined();

        $result = $this->repository->persistInstitution($institutionMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     * @throws InstitutionPersistException
     */
    public function testPersistInstitutionShouldReturnException(): void
    {
        $idMock = $this->createMock(InstitutionId::class);
        $idMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $institutionMock = $this->createMock(Institution::class);
        $institutionMock->expects(self::once())
            ->method('id')
            ->willReturn($idMock);

        $this->dataTransformer->expects(self::once())
            ->method('write')
            ->with($institutionMock)
            ->willReturnSelf();

        $this->dataTransformer->expects(self::once())
            ->method('read')
            ->willReturn([]);

        Redis::shouldReceive('set')
            ->once()
            ->with('institution::2', '[]')
            ->andThrow(new \Exception('testing'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('testing');

        $this->expectException(InstitutionPersistException::class);
        $this->expectExceptionMessage('It could not persist Institution with key institution::2');

        $this->repository->persistInstitution($institutionMock);
    }
}
