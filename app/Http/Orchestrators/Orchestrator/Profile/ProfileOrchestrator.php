<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:06:59
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;

/**
 * @codeCoverageIgnore
 */
abstract class ProfileOrchestrator implements Orchestrator
{
    public function __construct(
        protected readonly ProfileManagementContract $profileManagement
    ) {
    }
}
