<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:03:16
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Http\Request;

class GetUserOrchestrator extends UserOrchestrator
{
    public function __construct(UserManagementContract $userManagement)
    {
        parent::__construct($userManagement);
    }

    /**
     * @param Request $request
     * @return User|null
     */
    public function make(Request $request): ?User
    {
        if ($request->filled('login')) {
            return $this->userManagement->searchUserByLogin($request->input('login'));
        }

        return $this->userManagement->searchUserById($request->input('userId'));
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-user';
    }
}
