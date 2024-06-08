<?php

namespace App\Http\Controllers;

use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Profile\StoreProfileRequest;
use Core\Profile\Domain\Profile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller implements HasMiddleware
{
    private OrchestratorHandlerContract $orchestratorHandler;
    private ActionExecutorHandler $actionExecutorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        ActionExecutorHandler $actionExecutorHandler,
        ViewFactory $viewFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($logger, $viewFactory);

        $this->orchestratorHandler = $orchestratorHandler;
        $this->actionExecutorHandler = $actionExecutorHandler;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('profile.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws Exception
     */
    public function getProfiles(Request $request): JsonResponse
    {
        return $this->orchestratorHandler->handler('retrieve-profiles', $request);
    }

    public function changeStateProfile(Request $request): JsonResponse
    {
        try {
            /** @var Profile $profile */
            $profile = $this->orchestratorHandler->handler('change-state-profile', $request);

            ProfileUpdatedOrDeletedEvent::dispatch($profile->id()->value());
            RefreshModulesSession::dispatch();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    public function deleteProfile(Request $request, int $id): JsonResponse
    {
        try {
            $request->mergeIfMissing(['profileId' => $id]);

            $this->orchestratorHandler->handler('delete-profile', $request);
            ProfileUpdatedOrDeletedEvent::dispatch($id);

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    public function getProfile(Request $request, ?int $id = null): JsonResponse
    {
        $request->mergeIfMissing(['profileId' => $id]);
        $dataProfile = $this->orchestratorHandler->handler('detail-profile', $request);

        $view = $this->viewFactory->make('profile.profile-form', $dataProfile)
            ->render();

        return $this->renderView($view);
    }

    public function storeProfile(StoreProfileRequest $request): JsonResponse
    {
        try {
            $method = (is_null($request->input('id'))) ? 'create-profile-action' : 'update-profile-action';

            /** @var Profile $profile */
            $profile = $this->actionExecutorHandler->invoke($method, $request);

            ProfileUpdatedOrDeletedEvent::dispatch($profile->id()->value());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getProfiles', 'getProfile',
            ]),
        ];
    }
}
