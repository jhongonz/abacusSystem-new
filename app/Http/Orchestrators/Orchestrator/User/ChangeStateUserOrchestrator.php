<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:22:20
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Exception;
use Illuminate\Http\Request;

class ChangeStateUserOrchestrator extends UserOrchestrator
{
    public function __construct(UserManagementContract $userManagement)
    {
        parent::__construct($userManagement);
    }

    /**
     * @throws Exception
     */
    public function make(Request $request): User
    {
        $userId = $request->input('userId');
        $state = $request->input('state');

        $user = $this->userManagement->searchUserById($userId);

        $user->state()->setValue($state);
        $dataUpdate['state'] = $state;

        $this->userManagement->updateUser($user->id()->value(), $dataUpdate);

        return $user;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-user';
    }
}
