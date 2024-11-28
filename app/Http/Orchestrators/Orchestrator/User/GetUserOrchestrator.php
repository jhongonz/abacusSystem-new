<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:03:16
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;

class GetUserOrchestrator extends UserOrchestrator
{
    public function __construct(UserManagementContract $userManagement)
    {
        parent::__construct($userManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        if ($request->filled('login')) {
            $user = $this->userManagement->searchUserByLogin($request->string('login'));
        } else {
            $user = $this->userManagement->searchUserById($request->integer('userId'));
        }

        return ['user' => $user];
    }

    public function canOrchestrate(): string
    {
        return 'retrieve-user';
    }
}
