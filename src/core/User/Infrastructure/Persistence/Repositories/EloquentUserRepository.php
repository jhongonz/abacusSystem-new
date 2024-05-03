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
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use Exception;
use Illuminate\Database\DatabaseManager;

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
        UserModel $model,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->database = $database;
        $this->userTranslator = $userTranslator;
        $this->priority = $priority;
        $this->model = $model;
    }

    /**
     * @throws Exception
     */
    public function find(UserId $id): null|User
    {
        $builder = $this->database->table($this->getTable())
            ->where('user_id',$id->value())
            ->where('user_state','>', ValueObjectStatus::STATE_DELETE);
        $data = $builder->first();

        if (is_null($data)) {
            throw new UserNotFoundException('User not found with id: '. $id->value());
        }

        $userModel = $this->updateAttributesModelUser((array) $data);
        return $this->userTranslator->setModel($userModel)->toDomain();
    }

    /**
     * @throws Exception
     */
    public function findCriteria(UserLogin $login): null|User
    {
        $data = $this->database->table($this->getTable())
            ->where('user_login', $login->value())
            ->where('user_state','>', ValueObjectStatus::STATE_DELETE)
            ->first();

        if (is_null($data)) {
            throw new UserNotFoundException('User not found with login: '. $login->value());
        }

        $userModel = $this->updateAttributesModelUser((array) $data);
        return $this->userTranslator->setModel($userModel)->toDomain();
    }

    /**
     * @throws Exception
     */
    public function persistUser(User $user): User
    {
        $userModel = $this->domainToModel($user);
        $dataModel = $userModel->toArray();

        $builder = $this->database->table($this->getTable());

        $userId = $userModel->id();
        if (is_null($userId)) {
            $userId = $builder->insertGetId($dataModel);
            $user->id()->setValue($userId);
        } else {
            $builder->where('user_id', $userId);
            $builder->update($dataModel);
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

    /**
     * @throws UserNotFoundException
     */
    public function delete(UserId $id): void
    {
        $builder = $this->database->table($this->getTable());
        $dataUser = $builder->find($id->value());

        if (is_null($dataUser)) {
            throw new UserNotFoundException('User not found with id: '. $id->value());
        }

        $builder->where('user_id', $id->value());
        $builder->delete();
    }

    /**
     * @throws Exception
     */
    private function domainToModel(User $domain): UserModel
    {
        $builder = $this->database->table($this->getTable());
        $data = $builder->find($domain->id()->value());
        $model = $this->updateAttributesModelUser($data);

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

    private function updateAttributesModelUser(array $data = []): UserModel
    {
        $this->model->fill($data);
        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }
}
