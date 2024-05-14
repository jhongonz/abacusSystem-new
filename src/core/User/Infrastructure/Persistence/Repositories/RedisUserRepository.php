<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Repositories;

use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Exceptions\UserPersistException;
use Exception;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class RedisUserRepository implements ChainPriority, UserRepositoryContract
{
    /** @var int */
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const USER_KEY_FORMAT = '%s::%s';

    private int $priority;

    private string $keyPrefix;

    private UserFactoryContract $userFactory;

    private UserDataTransformerContract $dataTransformer;

    private LoggerInterface $logger;

    public function __construct(
        UserFactoryContract $userFactory,
        UserDataTransformerContract $dataTransformer,
        LoggerInterface $logger,
        string $keyPrefix = 'user',
        int $priority = self::PRIORITY_DEFAULT,
    ) {
        $this->userFactory = $userFactory;
        $this->dataTransformer = $dataTransformer;
        $this->logger = $logger;
        $this->keyPrefix = $keyPrefix;
        $this->priority = $priority;
    }

    /**
     * @throws UserNotFoundException
     */
    public function findCriteria(UserLogin $login): ?User
    {
        try {
            $data = Redis::get($this->userLoginKey($login));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new UserNotFoundException('User not found by login '.$login->value());
        }

        if (! is_null($data)) {
            $dataArray = json_decode($data, true);

            /** @var User */
            return $this->userFactory->buildUserFromArray($dataArray);
        }

        return null;
    }

    /**
     * @throws UserNotFoundException
     */
    public function find(UserId $id): ?User
    {
        try {
            $data = Redis::get($this->userKey($id));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new UserNotFoundException('User not found by id '.$id->value());
        }

        if (! is_null($data)) {
            $dataArray = json_decode($data, true);

            /** @var User */
            return $this->userFactory->buildUserFromArray($dataArray);
        }

        return null;
    }

    /**
     * @throws UserPersistException
     */
    public function persistUser(User $user): User
    {
        $userLoginKey = $this->userLoginKey($user->login());
        $userKey = $this->userKey($user->id());

        $userData = $this->dataTransformer->write($user)->read();
        try {
            Redis::set($userLoginKey, json_encode($userData));
            Redis::set($userKey, json_encode($userData));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new UserPersistException('It could not persist User with key '.$userKey.' in redis');
        }

        return $user;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function changePriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    private function userLoginKey(UserLogin $login): string
    {
        return sprintf(self::USER_KEY_FORMAT, $this->keyPrefix, $login->value());
    }

    private function userKey(UserId $id): string
    {
        return sprintf(self::USER_KEY_FORMAT, $this->keyPrefix, $id->value());
    }

    public function delete(UserId $id): void
    {
        Redis::delete($this->userKey($id));
    }
}
