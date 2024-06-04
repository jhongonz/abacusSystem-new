<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 17:04:21
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;

class DeleteUserOrchestrator extends UserOrchestrator
{
    public function __construct(UserManagementContract $userManagement)
    {
        parent::__construct($userManagement);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function make(Request $request): bool
    {
        $this->userManagement->deleteUser($request->input('userId'));
        return true;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'delete-user';
    }
}
