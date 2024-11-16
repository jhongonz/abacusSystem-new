<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:42:53
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;

class UpdateUserOrchestrator extends UserOrchestrator
{
    public function __construct(
        UserManagementContract $userManagement,
    ) {
        parent::__construct($userManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        /** @var array<string, mixed> $dataUpdateUser */
        $dataUpdateUser = json_decode($request->string('dataUpdate'), true);

        $user = $this->userManagement->updateUser($request->integer('userId'), $dataUpdateUser);

        return ['user' => $user];
    }

    public function canOrchestrate(): string
    {
        return 'update-user';
    }
}
