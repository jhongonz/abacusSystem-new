<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RecoveryAccountRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Traits\UserTrait;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as ResponseFoundation;

class UserController extends Controller implements HasMiddleware
{
    use UserTrait;

    private UserFactoryContract $userFactory;

    private UserManagementContract $userService;

    private EmployeeFactoryContract $employeeFactory;

    private EmployeeManagementContract $employeeService;
    private ViewFactory $viewFactory;

    public function __construct(
        UserFactoryContract $userFactory,
        UserManagementContract $userService,
        EmployeeFactoryContract $employeeFactory,
        EmployeeManagementContract $employeeService,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($logger);

        $this->userFactory = $userFactory;
        $this->userService = $userService;
        $this->employeeFactory = $employeeFactory;
        $this->employeeService = $employeeService;
        $this->viewFactory = $viewFactory;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('user.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function recoveryAccount(): JsonResponse|string
    {
        $view = $this->viewFactory->make('user.recovery-account')->render();

        return $this->renderView($view);
    }

    public function validateAccount(RecoveryAccountRequest $request): void
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

            return new RedirectResponse(route('index'));
        }

        $view = $this->viewFactory->make('user.restart-password', [
                'idUser' => $id,
                'activeLink' => true,
            ])
            ->render();

        return new Response($view);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $idUser = $this->userFactory->buildId(
            $request->input('idUser')
        );

        $dataUpdate = [
            'state' => ValueObjectStatus::STATE_ACTIVE,
            'password' => $this->makeHashPassword($request->input('password')),
        ];

        $this->userService->updateUser($idUser, $dataUpdate);

        return new JsonResponse(status: ResponseFoundation::HTTP_CREATED);
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
