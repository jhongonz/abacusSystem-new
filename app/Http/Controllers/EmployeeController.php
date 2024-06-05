<?php

namespace App\Http\Controllers;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Events\User\UserUpdateOrDeleteEvent;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Traits\MultimediaTrait;
use Core\Employee\Domain\Employee;
use Core\User\Domain\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller implements HasMiddleware
{
    use MultimediaTrait;

    private OrchestratorHandlerContract $orchestratorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestrators,
        ImageManagerInterface $imageManager,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($logger, $viewFactory);
        $this->setImageManager($imageManager);

        $this->orchestratorHandler = $orchestrators;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function getEmployees(Request $request): JsonResponse
    {
        return $this->orchestratorHandler->handler('retrieve-employees', $request);
    }

    public function changeStateEmployee(Request $request): JsonResponse
    {
        $employeeId = $request->input('id');

        try {
            /** @var Employee $employee */
            $employee = $this->orchestratorHandler->handler('change-state-employee', $request);
        } catch (Exception $exception) {
            $this->logger->error('Employee can not be updated with id: '.$employeeId, $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userId = $employee->userId()->value();
        if (! is_null($userId)) {

            $request->mergeIfMissing([
                'userId' => $userId,
                'state' => $employee->state()->value()
            ]);

            $this->orchestratorHandler->handler('change-state-user', $request);
            UserUpdateOrDeleteEvent::dispatch($userId);
        }

        return new JsonResponse(status:Response::HTTP_CREATED);
    }

    public function getEmployee(Request $request, ?int $employeeId = null): JsonResponse|string
    {
        $request->mergeIfMissing(['employeeId' => $employeeId]);
        $dataEmployee = $this->orchestratorHandler->handler('detail-employee', $request);

        $view = $this->viewFactory->make('employee.employee-form', $dataEmployee)
            ->render();

        return $this->renderView($view);
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            $method = (is_null($request->input('employeeId'))) ? 'createEmployee' : 'updateEmployee';

            /** @var Employee $employee */
            $employee = $this->{$method}($request);

            EmployeeUpdateOrDeletedEvent::dispatch($employee->id()->value());
            UserUpdateOrDeleteEvent::dispatch($employee->userId()->value());
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
        $random = Str::random(10);
        $imageUrl = $this->saveImageTmp($request->file('file')->getRealPath(), $random);

        return new JsonResponse(['token' => $random, 'url' => $imageUrl], Response::HTTP_CREATED);
    }

    public function deleteEmployee(Request $request, int $employeeId): JsonResponse
    {
        $request->mergeIfMissing(['employeeId' => $employeeId]);

        /** @var Employee $employee */
        $employee = $this->orchestratorHandler->handler('get-employee', $request);

        try {

            $this->orchestratorHandler->handler('delete-employee', $request);
            $this->deleteImage($employee->image()->value());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userId = $employee->userId()->value();
        if (! is_null($userId)) {
            $request->mergeIfMissing(['userId' => $userId]);
            $this->orchestratorHandler->handler('delete-user', $request);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    private function updateEmployee(StoreEmployeeRequest $request): Employee
    {
        $employee = $this->orchestratorHandler->handler('update-employee', $request);
        $this->updateUser($request);

        return $employee;
    }

    private function updateUser(StoreEmployeeRequest $request): User
    {
        return $this->orchestratorHandler->handler('update-user', $request);
    }

    private function createEmployee(StoreEmployeeRequest $request): Employee
    {
        /** @var Employee $employee */
        $employee = $this->orchestratorHandler->handler('create-employee', $request);

        $user = $this->createUser($request, $employee);
        $employee->userId()->setValue($user->id()->value());

        return $employee;
    }

    private function createUser(StoreEmployeeRequest $request, Employee $employee): User
    {
        $request->mergeIfMissing(['image' => $employee->image()->value()]);
        $request->mergeIfMissing(['employeeId' => $employee->id()->value()]);

        return $this->orchestratorHandler->handler('create-user', $request);
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
