<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 14:06:59
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;

abstract class ProfileOrchestrator implements Orchestrator
{
    protected ProfileManagementContract $profileManagement;
    public function __construct(
        ProfileManagementContract $profileManagement
    ) {
        $this->profileManagement = $profileManagement;
    }
}
