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
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class CampusController extends Controller
{
    private OrchestratorHandlerContract $orchestrators;
    private ActionExecutorHandler $actionExecutorHandler;
    private Session $session;

    public function __construct(
        OrchestratorHandlerContract $orchestrators,
        ActionExecutorHandler $actionExecutorHandler,
        Session $session,
        LoggerInterface $logger,
        ViewFactory $viewFactory
    ) {
        parent::__construct($logger, $viewFactory);
        $this->session = $session;
        $this->orchestrators = $orchestrators;
        $this->actionExecutorHandler = $actionExecutorHandler;
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
            $method = (! $request->filled('campusId')) ? 'create-campus-action' : 'update-campus-action';

            /** @var Campus $campus */
            $campus = $this->actionExecutorHandler->invoke($method, $request);

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
}
