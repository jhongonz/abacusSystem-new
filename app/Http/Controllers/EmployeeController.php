<?php

namespace App\Http\Controllers;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Traits\DataTablesTrait;
use App\Traits\MultimediaTrait;
use App\Traits\UserTrait;
use Core\Employee\Domain\Employee;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller implements HasMiddleware
{
    use MultimediaTrait;
    use UserTrait;
    use DataTablesTrait;

    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly ActionExecutorHandler $actionExecutorHandler,
        private readonly DataTables $dataTables,
        protected ImageManagerInterface $imageManager,
        protected ViewFactory $viewFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($logger);
        $this->setImageManager($imageManager);
        $this->setViewFactory($this->viewFactory);
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function getEmployees(Request $request): JsonResponse
    {
        $dataEmployees = $this->orchestrators->handler('retrieve-employees', $request);

        $collection = new Collection($dataEmployees);
        $datatable = $this->dataTables->collection($collection);
        $datatable->addColumn('tools', function (array $element): string {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public function changeStateEmployee(Request $request): JsonResponse
    {
        try {
            /** @var Employee $employee */
            $employee = $this->orchestrators->handler('change-state-employee', $request);
        } catch (Exception $exception) {
            $this->logger->error('Employee can not be updated with id: '.$request->input('id'), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userId = $employee->userId()->value();
        if (isset($userId)) {

            $request->merge([
                'userId' => $userId,
                'state' => $employee->state()->value()
            ]);

            $this->orchestrators->handler('change-state-user', $request);
            UserUpdateOrDeleteEvent::dispatch($userId);
        }

        return new JsonResponse(status:Response::HTTP_CREATED);
    }

    public function getEmployee(Request $request, ?int $employeeId = null): JsonResponse|string
    {
        $request->merge(['employeeId' => $employeeId]);
        $dataEmployee = $this->orchestrators->handler('detail-employee', $request);

        $view = $this->viewFactory->make('employee.employee-form', $dataEmployee)
            ->render();

        return $this->renderView($view);
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            $method = (! $request->filled('employeeId')) ? 'create-employee-action' : 'update-employee-action';

            /** @var Employee $employee */
            $employee = $this->actionExecutorHandler->invoke($method, $request);

            EmployeeUpdateOrDeletedEvent::dispatch((int) $employee->id()->value());
            UserUpdateOrDeleteEvent::dispatch((int) $employee->userId()->value());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'userId' => $employee->userId()->value(),
                'employeeId' => $employee->id()->value(),
            ],
            Response::HTTP_CREATED
        );
    }

    public function setImageEmployee(Request $request): JsonResponse
    {
        $uploadedFile = $request->file('file');
        if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {

            $random = Str::random(10);
            $imageUrl = $this->saveImageTmp($uploadedFile->getRealPath(), $random);

            return new JsonResponse(['token' => $random, 'url' => $imageUrl], Response::HTTP_CREATED);
        }

        return new JsonResponse(['msg' => 'Could not upload file.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function deleteEmployee(Request $request, int $employeeId): JsonResponse
    {
        $request->merge(['employeeId' => $employeeId]);

        /** @var Employee $employee */
        $employee = $this->orchestrators->handler('retrieve-employee', $request);

        try {
            $this->orchestrators->handler('delete-employee', $request);

            $image = $employee->image()->value();
            if (!is_null($image)) {
                $this->deleteImage($image);
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userId = $employee->userId()->value();
        if (isset($userId)) {
            $request->merge(['userId' => $userId]);
            $this->orchestrators->handler('delete-user', $request);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getEmployees', 'setImageEmployee', 'deleteEmployee', 'changeStateEmployee', 'storeEmployee',
            ]),
        ];
    }
}
