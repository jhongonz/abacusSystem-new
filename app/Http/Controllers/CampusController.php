<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Campus\StoreCampusRequest;
use Core\Campus\Domain\Campus;
use Core\Employee\Domain\Employee;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class CampusController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly ActionExecutorHandler $actionExecutorHandler,
        private readonly Session $session,
        LoggerInterface $logger,
        ViewFactory $viewFactory
    ) {
        parent::__construct($logger, $viewFactory);
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('campus.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function getCampusCollection(Request $request): JsonResponse
    {
        /** @var Employee $employee */
        $employee = $this->session->get('employee');
        $request->merge(['institutionId' => $employee->institutionId()->value()]);

        return $this->orchestrators->handler('retrieve-campus-collection', $request);
    }

    public function getCampus(Request $request, ?int $campusId = null): JsonResponse|string
    {
        $request->merge(['campusId' => $campusId]);
        $dataCampus = $this->orchestrators->handler('detail-campus', $request);

        $view = $this->viewFactory->make('campus.campus-form', $dataCampus)
            ->render();

        return $this->renderView($view);
    }

    public function storeCampus(StoreCampusRequest $request): JsonResponse
    {
        /** @var Employee $employee */
        $employee = $this->session->get('employee');
        $request->merge(['institutionId' => $employee->institutionId()->value()]);

        try {
            $method = (! $request->filled('campusId')) ? 'create-campus' : 'update-campus';

            /** @var Campus $campus */
            $campus = $this->orchestrators->handler($method, $request);

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['campusId' => $campus->id()->value()], Response::HTTP_CREATED);
    }

    public function changeStateCampus(Request $request): JsonResponse
    {
        try {
            $this->orchestrators->handler('change-state-campus', $request);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status:Response::HTTP_CREATED);
    }

    public function deleteCampus(Request $request, int $campusId): JsonResponse
    {
        $request->merge(['campusId' => $campusId]);

        try {
            $this->orchestrators->handler('delete-campus', $request);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getCampusCollection', 'deleteCampus', 'changeStateCampus', 'storeCampus','getCampus'
            ]),
        ];
    }
}
