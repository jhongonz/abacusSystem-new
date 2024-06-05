<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:42:53
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use App\Traits\UserTrait;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class UpdateUserOrchestrator extends UserOrchestrator
{
    use UserTrait;

    public function __construct(
        UserManagementContract $userManagement,
        Hasher $hasher
    ) {
        parent::__construct($userManagement);
        $this->setHasher($hasher);
    }

    /**
     * @param Request $request
     * @return User
     */
    public function make(Request $request): User
    {
        $dataUpdateUser = [
            'profileId' => $request->input('profile'),
            'login' => $request->input('login'),
            'state' => $request->input('state')
        ];

        $password = $request->input('password');
        if (! is_null($password)) {
            $dataUpdateUser['password'] = $this->makeHashPassword($password);
        }

        return $this->userManagement->updateUser($request->input('userId'), $dataUpdateUser);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-user';
    }
}
