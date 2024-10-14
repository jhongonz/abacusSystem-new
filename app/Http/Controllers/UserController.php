<?php

namespace App\Http\Controllers;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\User\RecoveryAccountRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Traits\UserTrait;
use Core\Employee\Domain\Employee;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as ResponseFoundation;

class UserController extends Controller implements HasMiddleware
{
    use UserTrait;

    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        protected Hasher $hasher,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
        private readonly UrlGenerator $urlGenerator
    ) {
        parent::__construct($logger, $viewFactory);
        $this->setHasher($hasher);
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

    public function validateAccount(RecoveryAccountRequest $request): JsonResponse
    {
        /** @var Employee $employee */
        $employee = $this->orchestrators->handler('retrieve-employee', $request);

        $link = $this->urlGenerator->route('user.reset-account', ['id' => $employee->userId()->value()]);

        return new JsonResponse(['link' => $link]);
    }

    public function resetAccount(int $id): Response
    {
        $view = $this->viewFactory->make('user.restart-password', [
            'userId' => $id,
            'activeLink' => true,
        ])
        ->render();

        return new Response($view);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $dataUpdate = [
            'state' => ValueObjectStatus::STATE_ACTIVE,
            'password' => $this->makeHashPassword($request->input('password')),
        ];

        $request->merge(['dataUpdate' => $dataUpdate]);
        $this->orchestrators->handler('update-user', $request);
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
