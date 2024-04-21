<?php

namespace Tests\Feature\Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\UpdateUser\UpdateUser;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Mockery\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(UpdateUser::class)]
class UpdateUserTest extends TestCase
{
    private UserRepositoryContract|Mock $repository;
    private UpdateUser $useCase;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository =  $this->createMock(UserRepositoryContract::class);
        $this->useCase = new UpdateUser($this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->useCase);
        parent::tearDown();
    }
}
