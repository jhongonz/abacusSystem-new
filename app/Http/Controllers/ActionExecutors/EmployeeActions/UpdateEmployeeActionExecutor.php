<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 22:04:33
 */

namespace App\Http\Controllers\ActionExecutors\EmployeeActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Traits\MultimediaTrait;
use App\Traits\UserTrait;
use Core\Employee\Domain\Employee;
use Core\User\Domain\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class UpdateEmployeeActionExecutor extends EmployeeActionExecutor
{
    use MultimediaTrait;
    use UserTrait;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        protected ImageManagerInterface $imageManager,
        protected Hasher $hasher,
    ) {
        parent::__construct($orchestratorHandler);
        $this->setImageManager($imageManager);
        $this->setHasher($hasher);
    }

    public function invoke(Request $request): Employee
    {
        $birthdate = $request->date('birthdate', 'd/m/Y');
        $dataUpdate = [
            'identification' => $request->input('identifier'),
            'identification_type' => $request->input('typeDocument'),
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
            'birthdate' => $birthdate,
        ];

        if ($request->filled('token')) {
            $filename = $this->saveImage($request->string('token'));
            $dataUpdate['image'] = $filename;
        }
        $request->merge(['dataUpdate' => json_encode($dataUpdate)]);

        /** @var array{employee: Employee} $dataEmployee */
        $dataEmployee = $this->orchestratorHandler->handler('update-employee', $request);
        $employee = $dataEmployee['employee'];

        $dataUpdateUser = [
            'profileId' => $request->input('profile'),
            'login' => $request->input('login'),
        ];

        if (isset($dataUpdate['image'])) {
            $dataUpdateUser['image'] = $dataUpdate['image'];
        }

        if ($request->filled('password')) {
            $dataUpdateUser['password'] = $this->makeHashPassword($request->string('password'));
        }

        $request->merge(['dataUpdate' => json_encode($dataUpdateUser)]);

        $actionUser = ($request->filled('userId')) ? 'update-user' : 'create-user';

        /** @var array{user: User} $dataUser */
        $dataUser = $this->orchestratorHandler->handler($actionUser, $request);
        $user = $dataUser['user'];

        if (!is_null($user->id()->value())) {
            $employee->userId()->setValue($user->id()->value());
        }

        return $employee;
    }

    public function canExecute(): string
    {
        return 'update-employee-action';
    }
}
