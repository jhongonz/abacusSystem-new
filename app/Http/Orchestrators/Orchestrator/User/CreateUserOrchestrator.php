<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:08:51
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use App\Traits\UserTrait;
use App\Traits\UtilsDateTimeTrait;
use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class CreateUserOrchestrator extends UserOrchestrator
{
    use UserTrait;
    use UtilsDateTimeTrait;

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
        $dataUser = [
            'id' => null,
            'employeeId' => $request->input('employeeId'),
            'profileId' => $request->input('profile'),
            'login' => $request->input('login'),
            'password' => $this->makeHashPassword($request->input('password')),
            'photo' => $request->input('image') ?? '',
            'state' => ValueObjectStatus::STATE_NEW,
            'createdAt' => $this->getCurrentTime(),
        ];

        return $this->userManagement->createUser([User::TYPE => $dataUser]);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'create-user';
    }
}
