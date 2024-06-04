<?php

namespace App\Http\Controllers;

use App\Http\Exceptions\ProfileNotActiveException;
use App\Http\Requests\User\LoginRequest;
use App\Traits\UserTrait;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Exception;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as ResponseSymfony;

class SecurityController extends Controller implements HasMiddleware
{
    use UserTrait;

    private UserManagementContract $userService;
    private EmployeeManagementContract $employeeService;
    private ProfileManagementContract $profileService;
    private StatefulGuard $guard;

    public function __construct(
        UserManagementContract $userService,
        EmployeeManagementContract $employeeService,
        ProfileManagementContract $profileService,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
        StatefulGuard $guard
    ) {
        parent::__construct($logger, $viewFactory);

        $this->userService = $userService;
        $this->employeeService = $employeeService;
        $this->profileService = $profileService;
        $this->guard = $guard;
    }

    public function index(): Response
    {
        $html = $this->viewFactory->make('home.login')->render();
        return new Response($html);
    }

    public function authenticate(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $user = $this->userService->searchUserByLogin($request->input('login'));

        try {
            $employee = $this->getEmployee($user);
            $profile = $this->getProfile($user);

            $credentials = [
                'user_login' => $user->login()->value(),
                'password' => $request->input('password'),
                'user_id' => $user->id()->value(),
            ];

            if ($this->guard->attempt($credentials)) {
                session()->regenerate();
                session()->put([
                    'user' => $user,
                    'profile' => $profile,
                    'employee' => $employee,
                ]);

                if ($request->ajax()) {
                    return new JsonResponse(status:ResponseSymfony::HTTP_OK);
                }

                return new RedirectResponse('/home');
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return ! $request->ajax() ?
            new RedirectResponse('/login') :
            new JsonResponse(['message' => 'Bad credentials'], ResponseSymfony::HTTP_BAD_REQUEST);
    }

    public function home(): JsonResponse|string
    {
        $view = $this->viewFactory->make('home.index')->render();
        return $this->renderView($view);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->guard->logout();

        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new RedirectResponse(route('panel.login'));
    }

    private function getEmployee(User $user): Employee
    {
        return $this->employeeService->searchEmployeeById($user->employeeId()->value());
    }

    /**
     * @throws ProfileNotActiveException
     */
    private function getProfile(User $user): ?Profile
    {
        $profile = $this->profileService->searchProfileById($user->profileId()->value());

        if ($profile instanceof Profile && $profile->state()->isInactivated()) {
            $this->logger->warning("User's profile with id: ".$profile->id()->value().' is not active');
            throw new ProfileNotActiveException('User is not authorized, contact with administrator');
        }

        return $profile;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', only: ['home']),
        ];
    }
}
