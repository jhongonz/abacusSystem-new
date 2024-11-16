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
     * @return array<null>
     */
    public function make(Request $request): array
    {
        $this->userManagement->deleteUser($request->integer('userId'));

        return [];
    }

    public function canOrchestrate(): string
    {
        return 'delete-user';
    }
}
