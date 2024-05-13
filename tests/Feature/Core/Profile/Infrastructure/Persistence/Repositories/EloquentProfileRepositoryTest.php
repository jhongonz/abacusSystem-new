<?php

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories;

use Core\Profile\Domain\Profiles;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile as ProfileModel;
use Core\Profile\Infrastructure\Persistence\Repositories\EloquentProfileRepository;
use Core\Profile\Infrastructure\Persistence\Translators\ProfileTranslator;
use Illuminate\Database\DatabaseManager;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EloquentProfileRepository::class)]
class EloquentProfileRepositoryTest extends TestCase
{
    private ProfileModel|MockObject $model;
    private ProfileTranslator|MockObject $translator;
    private DatabaseManager|MockInterface $databaseManager;
    private EloquentProfileRepository $repository;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = $this->createMock(ProfileModel::class);
        $this->translator = $this->createMock(ProfileTranslator::class);
        $this->databaseManager = $this->mock(DatabaseManager::class);
        $this->repository = new EloquentProfileRepository(
            $this->databaseManager,
            $this->translator,
            $this->model
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->model,
            $this->translator,
            $this->databaseManager,
            $this->repository
        );
        parent::tearDown();
    }

    public function test_priority_should_return_int(): void
    {
        $result = $this->repository->priority();

        $this->assertIsInt($result);
        $this->assertSame(50, $result);
    }

    public function test_changePriority_should_change_and_return_self(): void
    {
        $result = $this->repository->changePriority(100);

        $this->assertInstanceOf(EloquentProfileRepository::class, $result);
        $this->assertSame($this->repository, $result);
        $this->assertSame(100, $result->priority());
    }

    /**
     * @throws Exception
     */
    public function test_persistProfiles_should_return_object(): void
    {
        $profiles = $this->createMock(Profiles::class);
        $result = $this->repository->persistProfiles($profiles);

        $this->assertInstanceOf(Profiles::class, $result);
        $this->assertSame($profiles, $result);
    }
}
