<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:22:20
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;

class ChangeStateUserOrchestrator extends UserOrchestrator
{
    public function __construct(UserManagementContract $userManagement)
    {
        parent::__construct($userManagement);
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $dataUpdate['state'] = $request->integer('state');

        $user = $this->userManagement->updateUser($request->integer('userId'), $dataUpdate);
        return ['user' => $user];
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-user';
    }
}
