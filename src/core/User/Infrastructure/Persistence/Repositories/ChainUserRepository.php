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

class ChainUserRepository extends AbstractChainRepository implements UserRepositoryContract
{
    private const FUNCTION_NAME = 'persistUser';

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAME;
    }

    /**
     * @throws Throwable
     */
    public function find(UserId $id): ?User
    {
        try {
            /** @var User|null $result */
            $result = $this->read(__FUNCTION__, $id);

            return $result;
        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found by id '.$id->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function findCriteria(UserLogin $login): ?User
    {
        try {
            /** @var User|null $result */
            $result = $this->read(__FUNCTION__, $login);

            return $result;
        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found by login '.$login->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function delete(UserId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    /**
     * @throws Exception
     */
    public function persistUser(User $user): User
    {
        /** @var User $result */
        $result = $this->write(__FUNCTION__, $user);

        return $result;
    }
}
