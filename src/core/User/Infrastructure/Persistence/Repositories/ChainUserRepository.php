<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Repositories;

use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Exceptions\UserNotFoundException;
use Exception;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class ChainUserRepository extends AbstractChainRepository implements UserRepositoryContract
{
    private const FUNCTION_NAMES = [
        User::class => 'persistUser'
    ];

    private string $domainToPersist;
    private bool $deleteSource = false;

    function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws Throwable
     */
    public function find(UserId $id): null|User
    {
        $this->domainToPersist = User::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found by id '. $id->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function findCriteria(UserLogin $login): null|User
    {
        $this->domainToPersist = User::class;

        try {
            return $this->read(__FUNCTION__, $login);
        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found by login '. $login->value());
        }
    }

    /**
     * @throws UserNotFoundException
     * @throws Throwable
     */
    public function delete(UserId $id): void
    {
        $this->deleteSource = true;

        try {
            $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new UserNotFoundException($exception->getMessage());
        }
    }

    public function persistUser(User $user): User
    {
        $this->domainToPersist = User::class;

        return $this->write(__FUNCTION__, $user);
    }

    function functionNameDelete(): bool
    {
        return $this->deleteSource;
    }
}
