<?php

namespace App\Http\Controllers;

use App\Http\Exceptions\ProfileNotActiveException;
use App\Http\Requests\User\LoginRequest;
use App\Traits\UserTrait;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as ResponseSymfony;

class HomeController extends Controller implements HasMiddleware
{
    use UserTrait;

    private UserFactoryContract $userFactory;

    private UserManagementContract $userService;

    private EmployeeManagementContract $employeeService;

    private EmployeeFactoryContract $employeeFactory;

    private ProfileFactoryContract $profileFactory;

    private ProfileManagementContract $profileService;
    private ViewFactory $viewFactory;

    public function __construct(
        UserFactoryContract $userFactory,
        UserManagementContract $userService,
        EmployeeManagementContract $employeeService,
        EmployeeFactoryContract $employeeFactory,
        ProfileFactoryContract $profileFactory,
        ProfileManagementContract $profileService,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($logger);

        $this->userFactory = $userFactory;
        $this->userService = $userService;
        $this->employeeFactory = $employeeFactory;
        $this->employeeService = $employeeService;
        $this->profileFactory = $profileFactory;
        $this->profileService = $profileService;
        $this->viewFactory = $viewFactory;
    }

    public function index(): Response
    {
        $html = $this->viewFactory->make('home.login')->render();
        return new Response($html);
    }

    /**
     * @throws ProfileNotActiveException
     */
    public function authenticate(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $login = $this->userFactory->buildLogin($request->input('login'));
        $user = $this->userService->searchUserByLogin($login);

        $employee = $this->getEmployee($user);
        $profile = $this->getProfile($user);

        $credentials = [
            'user_login' => $user->login()->value(),
            'password' => $request->input('password'),
            'user_id' => $user->id()->value(),
        ];

        try {
            if (Auth::attempt($credentials)) {
                session()->regenerate();
                session()->put([
                    'user' => $user,
                    'profile' => $profile,
                    'employee' => $employee,
                ]);

                if ($request->ajax()) {
                    return new JsonResponse;
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
        Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new RedirectResponse(route('panel.login'));
    }

    private function getEmployee(User $user): ?Employee
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($user->employeeId()->value());

        try {
            return $this->employeeService->searchEmployeeById($employeeId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return null;
    }

    /**
     * @throws ProfileNotActiveException
     */
    private function getProfile(User $user): ?Profile
    {
        $profile = null;
        $profileId = $this->profileFactory->buildProfileId($user->profileId()->value());

        try {
            $profile = $this->profileService->searchProfileById($profileId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        if ($profile instanceof Profile && $profile->state()->isInactivated()) {
            $this->logger->warning("User's profile with id: ".$profileId->value().' is not active');
            throw new ProfileNotActiveException('User is not authorized, contact with administrator');
        }

        return $profile;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware('auth', only: ['home']),
        ];
    }
}
