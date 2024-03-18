<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RecoveryAccountRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\ValueObjects\UserState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as ResponseFundation;

class UserController extends Controller implements HasMiddleware
{
    private UserFactoryContract $userFactory;
    private UserManagementContract $userService;
    private EmployeeFactoryContract $employeeFactory;
    private EmployeeManagementContract $employeeService;

    public function __construct(
      UserFactoryContract $userFactory,
      UserManagementContract $userService,
      EmployeeFactoryContract $employeeFactory,
      EmployeeManagementContract $employeeService,
      LoggerInterface $logger,
    ) {
        parent::__construct($logger);

        $this->userFactory = $userFactory;
        $this->userService = $userService;
        $this->employeeFactory = $employeeFactory;
        $this->employeeService = $employeeService;
    }

    public function index(): JsonResponse|string
    {
        $view = view('user.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function recoveryAccout(): JsonResponse|string
    {
        $view = view('user.recovery-account')->render();
        return $this->renderView($view);
    }

    public function validateAccount(RecoveryAccountRequest $request)
    {
        $identification = $this->employeeFactory->buildEmployeeIdentification(
            $request->input('identification')
        );

        $employee = $this->employeeService->searchEmployeeByIdentification($identification);

        $link = url('reset'.'/'.$employee->user()->id()->value());
    }

    public function resetAccount(Request $request): Response|RedirectResponse
    {
        if (is_null($request->iduser)) {
            return redirect()->route('index');
        }

        $idUser = $request->iduser;
        return response()->view('user.restart-password',[
            'idUser' => $idUser,
            'activeLink' => true
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $idUser = $this->userFactory->buildId(
            $request->input('idUser')
        );

        $dataUpdate = [
            'state'=> UserState::STATE_ACTIVE,
            'password' => Hash::make($request->input('password'))
        ];

        $this->userService->updateUser($idUser, $dataUpdate);

        return response()->json(status:ResponseFundation::HTTP_CREATED);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return Middleware|array
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware('only.ajax-request', only: ['recoveryAccout','resetPassword']),
        ];
    }
}
