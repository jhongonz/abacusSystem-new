<?php

namespace Core\User\Infrastructure\Persistence\Repositories;

use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use App\Models\User as UserModel;
use Core\User\Domain\ValueObjects\UserState;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Persistence\Translators\TranslatorContract;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use Exception;

class EloquentUserRepository implements UserRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;
    
    private UserModel $userModel;
    private UserTranslator $userTranslator;
    private TranslatorContract $modelUserTranslator;
    private int $priority;
    
    public function __construct(
        UserModel $userModel,
        UserTranslator $userTranslator,
        TranslatorContract $modelUserTranslator,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->userModel = $userModel;
        $this->userTranslator = $userTranslator;
        $this->modelUserTranslator = $modelUserTranslator;
        $this->priority = $priority;
    }

    /**
     * @throws Exception
     */
    public function find(UserId $id): null|User
    {
        try {
            /** @var UserModel $userModel */
            $userModel = $this->userModel
                ->where('user_id', $id->value())
                ->where('user_state','>',UserState::STATE_DELETE)
                ->firstOrFail();
        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found with id: '. $id->value());
        }

        return $this->userTranslator->setModel($userModel)->toDomain();
    }

    /**
     * @throws Exception
     */
    public function findCriteria(UserLogin $login): null|User
    {
        try {
            /** @var UserModel $userModel */
            $userModel = $this->userModel
                ->where('user_login', $login->value())
                ->where('user_state','>',UserState::STATE_DELETE)
                ->firstOrFail();
        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found with login: '. $login->value());
        }
        
        return $this->userTranslator->setModel($userModel)->toDomain();
    }
    
    public function persistUser(User $user): User
    {
        /** @var UserModel $userModel */
        $userModel = $this->modelUserTranslator->executeTranslate($user);
        $userModel->save();
        
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

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function update(UserId $id, User $user): void
    {
        // TODO: Implement update() method.
    }

    public function delete(UserId $id): void
    {
        // TODO: Implement delete() method.
    }
}