<?php

namespace App\Http\Controllers;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use App\Events\EventDispatcher;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Campus\StoreCampusRequest;
use App\Traits\DataTablesTrait;
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
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception;

class CampusController extends Controller implements HasMiddleware
{
    use DataTablesTrait;

    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly Session $session,
        private readonly DataTables $datatables,
        private readonly EventDispatcher $eventDispatcher,
        protected ViewFactory $viewFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('campus.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws Exception
     */
    public function getCampusCollection(Request $request): JsonResponse
    {
        /** @var Employee $employee */
        $employee = $this->session->get('employee');
        $request->merge(['institutionId' => $employee->institutionId()->value()]);

        $dataCampus = $this->orchestrators->handler('retrieve-campus-collection', $request);

        $dataTable = $this->datatables->collection($dataCampus);
        $dataTable->addColumn('tools', function (array $element): string {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $dataTable->escapeColumns([])->toJson();
    }

    public function getCampus(Request $request, ?int $campusId = null): JsonResponse|string
    {
        $request->merge(['campusId' => $campusId]);

        /** @var array<string, mixed> $dataCampus */
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
            $method = (!$request->filled('campusId')) ? 'create-campus' : 'update-campus';

            /** @var array{campus: Campus} $dataCampus */
            $dataCampus = $this->orchestrators->handler($method, $request);
            $campus = $dataCampus['campus'];

            /** @var int $campusId */
            $campusId = $campus->id()->value();

            $this->eventDispatcher->dispatch(new CampusUpdatedOrDeletedEvent($campusId));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['campusId' => $campusId], Response::HTTP_CREATED);
    }

    public function changeStateCampus(Request $request): JsonResponse
    {
        try {
            $this->orchestrators->handler('change-state-campus', $request);

            $this->eventDispatcher->dispatch(new CampusUpdatedOrDeletedEvent($request->integer('campusId')));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
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
                'getCampusCollection', 'deleteCampus', 'changeStateCampus', 'storeCampus', 'getCampus',
            ]),
        ];
    }
}
