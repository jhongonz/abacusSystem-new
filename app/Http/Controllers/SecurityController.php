<?php

namespace App\Http\Controllers;

use App\Http\Exceptions\ProfileNotActiveException;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\User\LoginRequest;
use App\Traits\UserTrait;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Profile;
use Core\User\Domain\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Session\Session;
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

    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        protected ViewFactory $viewFactory,
        LoggerInterface $logger,
        private readonly StatefulGuard $guard,
        private readonly Session $session,
    ) {
        parent::__construct($logger);
    }

    public function index(): Response
    {
        $html = $this->viewFactory->make('home.login')->render();

        return new Response($html);
    }

    public function authenticate(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $dataUser = $this->orchestrators->handler('retrieve-user', $request);

        /** @var User $user */
        $user = $dataUser['user'];

        try {
            $employee = $this->getEmployee($request, $user);
            $profile = $this->getProfile($request, $user);

            $credentials = [
                'user_login' => $user->login()->value(),
                'password' => $request->input('password'),
                'user_id' => $user->id()->value(),
            ];

            if ($this->guard->attempt($credentials)) {
                $this->session->regenerate();
                $this->session->put([
                    'user' => $user,
                    'profile' => $profile,
                    'employee' => $employee,
                ]);

                if ($request->ajax()) {
                    return new JsonResponse(status: ResponseSymfony::HTTP_OK);
                }

                return new RedirectResponse('/home');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return !$request->ajax() ?
            new RedirectResponse('/login') :
            new JsonResponse(['message' => 'Bad credentials'], ResponseSymfony::HTTP_BAD_REQUEST);
    }

    public function home(): JsonResponse|string
    {
        $view = $this->viewFactory->make('home.index')->render();

        return $this->renderView($view);
    }

    public function logout(): RedirectResponse
    {
        $this->guard->logout();

        $this->session->flush();
        $this->session->invalidate();
        $this->session->regenerateToken();

        return new RedirectResponse(route('panel.login'));
    }

    private function getEmployee(Request $request, User $user): Employee
    {
        $request->merge(['employeeId' => $user->employeeId()->value()]);

        /** @var array<int|string, Employee> $dataResponse */
        $dataResponse = $this->orchestrators->handler('retrieve-employee', $request);

        return $dataResponse['employee'];
    }

    /**
     * @throws ProfileNotActiveException
     */
    private function getProfile(Request $request, User $user): Profile
    {
        $request->merge(['profileId' => $user->profileId()->value()]);

        /** @var array<string, Profile> $dataProfile */
        $dataProfile = $this->orchestrators->handler('retrieve-profile', $request);
        $profile = $dataProfile['profile'];

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
