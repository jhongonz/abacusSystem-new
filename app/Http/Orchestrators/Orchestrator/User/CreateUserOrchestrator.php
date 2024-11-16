<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 16:08:51
 */

namespace App\Http\Orchestrators\Orchestrator\User;

use App\Traits\UserTrait;
use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class CreateUserOrchestrator extends UserOrchestrator
{
    use UserTrait;

    public function __construct(
        UserManagementContract $userManagement,
        protected Hasher $hasher,
    ) {
        parent::__construct($userManagement);
        $this->setHasher($hasher);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $dataUser = [
            'id' => null,
            'employeeId' => $request->integer('employeeId'),
            'profileId' => $request->input('profile'),
            'login' => $request->input('login'),
            'password' => $this->makeHashPassword($request->string('password')),
            'photo' => $request->input('image') ?? null,
            'state' => ValueObjectStatus::STATE_NEW,
        ];

        $user = $this->userManagement->createUser([User::TYPE => $dataUser]);

        return ['user' => $user];
    }

    public function canOrchestrate(): string
    {
        return 'create-user';
    }
}
