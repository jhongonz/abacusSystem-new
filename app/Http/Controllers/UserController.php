<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RecoveryAccountRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Traits\UserTrait;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\ValueObjects\UserState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as ResponseFoundation;

class UserController extends Controller implements HasMiddleware
{
    use UserTrait;

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

    public function recoveryAccount(): JsonResponse|string
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

        $link = url('reset'.'/'.$employee->userId()->value());
    }

    public function resetAccount(?int $id = null): Response|RedirectResponse
    {
        try {
            Assertion::notNull($id);
        } catch (AssertionFailedException $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return redirect()->route('index');
        }

        return response()->view('user.restart-password', [
            'idUser' => $id,
            'activeLink' => true,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $idUser = $this->userFactory->buildId(
            $request->input('idUser')
        );

        $dataUpdate = [
            'state' => UserState::STATE_ACTIVE,
            'password' => $this->makeHashPassword($request->input('password')),
        ];

        $this->userService->updateUser($idUser, $dataUpdate);

        return response()->json(status: ResponseFoundation::HTTP_CREATED);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: ['recoveryAccout', 'resetPassword']),
        ];
    }
}
