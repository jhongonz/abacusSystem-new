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
use Core\User\Exceptions\UserNotFoundException;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User as UserModel;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use Exception;
use Illuminate\Database\DatabaseManager;

class EloquentUserRepository implements ChainPriority, UserRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    public function __construct(
        private readonly DatabaseManager $database,
        private readonly UserTranslator $userTranslator,
        private readonly UserModel $model,
        private int $priority = self::PRIORITY_DEFAULT
    ) {
    }

    /**
     * @throws Exception
     */
    public function find(UserId $id): ?User
    {
        $builder = $this->database->table($this->getTable())
            ->where('user_id', $id->value())
            ->where('user_state', '>', ValueObjectStatus::STATE_DELETE);
        $data = $builder->first();

        if (is_null($data)) {
            throw new UserNotFoundException('User not found with id: '.$id->value());
        }

        $userModel = $this->updateAttributesModelUser((array) $data);

        return $this->userTranslator->setModel($userModel)->toDomain();
    }

    /**
     * @throws Exception
     */
    public function findCriteria(UserLogin $login): ?User
    {
        $builder = $this->database->table($this->getTable())
            ->where('user_login', $login->value())
            ->where('user_state', '>', ValueObjectStatus::STATE_DELETE);
        $data = $builder->first();

        if (is_null($data)) {
            throw new UserNotFoundException('User not found with login: '.$login->value());
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
            $dataModel['created_at'] = $this->getDateTime();

            $userId = $builder->insertGetId($dataModel);
            $user->id()->setValue($userId);
        } else {
            $dataModel['updated_at'] = $this->getDateTime();

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
     * @throws Exception
     */
    public function delete(UserId $id): void
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('user_id', $id->value());
        $dataUser = $builder->first();

        if (is_null($dataUser)) {
            throw new UserNotFoundException('User not found with id: '.$id->value());
        }

        $userModel = $this->updateAttributesModelUser((array) $dataUser);
        $userModel->changeState(ValueObjectStatus::STATE_DELETE);
        $userModel->changeDeletedAt($this->getDateTime());

        $builder->update($userModel->toArray());
    }

    /**
     * @throws Exception
     */
    private function domainToModel(User $domain): UserModel
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('user_id', $domain->id()->value());
        $data = $builder->first();
        $model = $this->updateAttributesModelUser((array) $data);

        $model->changeId($domain->id()->value());
        $model->changeEmployeeId($domain->employeeId()->value());
        $model->changeProfileId($domain->profileId()->value());
        $model->changeLogin($domain->login()->value());
        $model->changePassword($domain->password()->value());
        $model->changeState($domain->state()->value());
        $model->changePhoto($domain->photo()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (! is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    /**
     * @param array<string, mixed> $data
     * @return UserModel
     */
    private function updateAttributesModelUser(array $data = []): UserModel
    {
        $this->model->fill($data);

        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * @throws Exception
     */
    private function getDateTime(): \DateTime
    {
        return new \DateTime('now');
    }
}
