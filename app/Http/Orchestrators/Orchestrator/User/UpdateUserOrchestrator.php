<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:42:53
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Http\Request;

class UpdateUserOrchestrator extends UserOrchestrator
{
    public function __construct(
        UserManagementContract $userManagement,
    ) {
        parent::__construct($userManagement);
    }

    /**
     * @param Request $request
     * @return User
     */
    public function make(Request $request): User
    {
        /** @var array<string, mixed> $dataUpdateUser */
        $dataUpdateUser = json_decode($request->string('dataUpdate'), true);
        return $this->userManagement->updateUser($request->integer('userId'), $dataUpdateUser);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-user';
    }
}
