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
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class UpdateEmployeeActionExecutor extends EmployeeActionExecutor
{
    use MultimediaTrait;
    use UserTrait;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        ImageManagerInterface $imageManager,
        Hasher $hasher
    ) {
        parent::__construct($orchestratorHandler);
        $this->setImageManager($imageManager);
        $this->setHasher($hasher);
    }

    /**
     * @param Request $request
     * @return Employee
     */
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
            'birthdate' => $birthdate->format('Y-m-d'),
        ];

        $imageToken = $request->input('token');
        if (isset($imageToken)) {
            $filename = $this->saveImage($imageToken);
            $dataUpdate['image'] = $filename;
        }
        $request->merge(['dataUpdate' => json_encode($dataUpdate)]);

        /** @var Employee $employee */
        $employee = $this->orchestratorHandler->handler('update-employee', $request);

        $dataUpdateUser = [
            'profileId' => $request->input('profile'),
            'login' => $request->input('login')
        ];

        if (isset($dataUpdate['image'])) {
            $dataUpdateUser['image'] = $dataUpdate['image'];
        }

        $password = $request->input('password');
        if (isset($password)) {
            $dataUpdateUser['password'] = $this->makeHashPassword($password);
        }

        $request->merge(['dataUpdate' => json_encode($dataUpdateUser)]);
        $this->orchestratorHandler->handler('update-user', $request);

        return $employee;
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'update-employee-action';
    }
}
