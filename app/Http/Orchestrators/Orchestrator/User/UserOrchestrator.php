<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:21:03
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\User\Domain\Contracts\UserManagementContract;

abstract class UserOrchestrator implements Orchestrator
{
    protected UserManagementContract $userManagement;

    public function __construct(
        UserManagementContract $userManagement
    ) {
        $this->userManagement = $userManagement;
    }
}
