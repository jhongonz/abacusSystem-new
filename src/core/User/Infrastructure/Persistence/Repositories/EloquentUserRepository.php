<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Repositories;

use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User as UserModel;
use Core\User\Exceptions\UserDeleteException;
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use Exception;
use Illuminate\Database\DatabaseManager;
use Throwable;

class EloquentUserRepository implements UserRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;
    private DatabaseManager $database;
    private UserTranslator $userTranslator;
    private UserModel $model;
    private int $priority;

    public function __construct(
        DatabaseManager $database,
        UserTranslator $userTranslator,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->database = $database;
        $this->userTranslator = $userTranslator;
        $this->priority = $priority;

        $this->model = new UserModel();
    }

    /**
     * @throws Exception
     */
    public function find(UserId $id): null|User
    {
        try {
            /** @var UserModel $userModel */
            $userModel = $this->database->table($this->model->getTable())
                ->where('user_id',$id->value())
                ->where('user_state','>', ValueObjectStatus::STATE_DELETE)
                ->first();

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
            $userModel = $this->database->table($this->model->getTable())
                ->where('user_login', $login->value())
                ->where('user_state','>', ValueObjectStatus::STATE_DELETE)
                ->first();

        } catch (Exception $exception) {
            throw new UserNotFoundException('User not found with login: '. $login->value());
        }

        return $this->userTranslator->setModel($userModel)->toDomain();
    }

    /**
     * @throws Exception
     */
    public function persistUser(User $user): User
    {
        $userModel = $this->domainToModel($user);
        $userModel->save();
        $user->id()->setValue($userModel->id());

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

    /**
     * @throws UserNotFoundException
     * @throws UserDeleteException
     */
    public function delete(UserId $id): void
    {
        /**@var UserModel $userModel */
        $userModel = $this->database->table($this->model->getTable())
            ->where('user_id', $id->value())
            ->where('user_state','>',ValueObjectStatus::STATE_DELETE)
            ->first();

        if (is_null($userModel)) {
            throw new UserNotFoundException('User not found with id: '. $id->value());
        }

        try {
            $userModel->deleteOrFail();
        } catch (Throwable $exception) {
            throw new UserDeleteException($exception->getMessage(), $exception->getTrace());
        }
    }

    /**
     * @throws Exception
     */
    protected function domainToModel(User $domain, ?UserModel $model = null): UserModel
    {
        if (is_null($model)) {
            $model = $this->find($domain->id()) ?: $this->createUserModel();
        }

        $model->changeId($domain->id()->value());
        $model->changeEmployeeId($domain->employeeId()->value());
        $model->changeProfileId($domain->profileId()->value());
        $model->changeLogin($domain->login()->value());
        $model->changePassword($domain->password()->value());
        $model->changeState($domain->state()->value());
        $model->changePhoto($domain->photo()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    protected function createUserModel(): UserModel
    {
        return $this->model;
    }
}
