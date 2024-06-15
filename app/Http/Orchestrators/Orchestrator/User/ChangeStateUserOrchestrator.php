<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:22:20
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use App\Traits\UtilsDateTimeTrait;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Exception;
use Illuminate\Http\Request;

class ChangeStateUserOrchestrator extends UserOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(UserManagementContract $userManagement)
    {
        parent::__construct($userManagement);
    }

    /**
     * @throws Exception
     */
    public function make(Request $request): User
    {
        $dataUpdate['state'] = $request->input('state');
        $dataUpdate['updatedAt'] = $this->getCurrentTime();

        return $this->userManagement->updateUser($request->input('userId'), $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-user';
    }
}
