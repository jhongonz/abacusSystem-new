<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Application\UseCases\UseCasesService;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;

class UpdateUser extends UseCasesService
{
    public function __construct(
        UserRepositoryContract $userRepository,
    ) {
        parent::__construct($userRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): User
    {
        $this->validateRequest($request, UpdateUserRequest::class);

        /** @var UpdateUserRequest $request */
        $user = $this->userRepository->find($request->userId());

        foreach ($request->data() as $field => $value) {
            $methodName = $this->getFunctionName($field);

            if (\is_callable([$this, $methodName])) {
                $user = $this->{$methodName}($user, $value);
            }
        }

        return $this->userRepository->persistUser($user);
    }

    protected function getFunctionName(string $field): string
    {
        return sprintf('change%s', ucfirst($field));
    }

    private function changeEmployeeId(User $user, int $value): User
    {
        $user->employeeId()->setValue($value);

        return $user;
    }

    private function changeProfileId(User $user, int $value): User
    {
        $user->profileId()->setValue($value);

        return $user;
    }

    private function changeLogin(User $user, string $value): User
    {
        $user->login()->setValue($value);

        return $user;
    }

    private function changePassword(User $user, string $value): User
    {
        $user->password()->setValue($value);

        return $user;
    }

    /**
     * @throws \Exception
     */
    private function changeState(User $user, int $value): User
    {
        $user->state()->setValue($value);

        return $user;
    }

    private function changeCreatedAt(User $user, \DateTime $value): User
    {
        $user->createdAt()->setValue($value);

        return $user;
    }

    private function changeUpdatedAt(User $user, \DateTime $value): User
    {
        $user->updatedAt()->setValue($value);

        return $user;
    }

    private function changeImage(User $user, string $image): User
    {
        $user->photo()->setValue($image);

        return $user;
    }
}
