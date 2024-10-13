<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:21:03
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\User\Domain\Contracts\UserManagementContract;

/**
 * @codeCoverageIgnore
 */
abstract class UserOrchestrator implements Orchestrator
{
    public function __construct(
        protected readonly UserManagementContract $userManagement
    ) {
    }
}
