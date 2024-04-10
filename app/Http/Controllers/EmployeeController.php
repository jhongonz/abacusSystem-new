<?php

namespace App\Http\Controllers;

use App\Events\User\UserUpdateOrDeleteEvent;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use Core\Employee\Application\Factory\EmployeeFactory;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\ValueObjects\UserId;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller implements HasMiddleware
{
    private EmployeeManagementContract $employeeService;
    private EmployeeFactory $employeeFactory;
    private EmployeeDataTransformerContract $employeeDataTransformer;
    private UserFactoryContract $userFactory;
    private UserManagementContract $userService;
    private ProfileManagementContract $profileService;
    private DataTables $dataTable;
    public function __construct(
        EmployeeManagementContract $employeeService,
        EmployeeFactory $employeeFactory,
        EmployeeDataTransformerContract $employeeDataTransformer,
        UserFactoryContract $userFactory,
        UserManagementContract $userService,
        ProfileManagementContract $profileService,
        DataTables $dataTable,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->employeeService = $employeeService;
        $this->employeeFactory = $employeeFactory;
        $this->employeeDataTransformer = $employeeDataTransformer;
        $this->userFactory = $userFactory;
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->dataTable = $dataTable;
    }

    public function index(): JsonResponse|string
    {
        $view = view('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function getEmployees(Request $request): JsonResponse
    {
        $employees = $this->employeeService->searchEmployees($request->filters);
        return $this->prepareListEmployees($employees);
    }

    public function changeStateEmployee(Request $request):JsonResponse
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($request->id);
        $employee = $this->employeeService->searchEmployeeById($employeeId);

        if ($employee->state()->isNew() || $employee->state()->isInactived()) {
            $employee->state()->activate();
        } else if ($employee->state()->isActived()) {
            $employee->state()->inactive();
        }

        $dataUpdate['state'] = $employee->state()->value();

        try {
            $this->employeeService->updateEmployee($employeeId, $dataUpdate);
        } catch (Exception $exception) {
            $this->logger->error('Employee can not be updated with id: '.$employeeId->value());

            return response()->json(status:Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!is_null($employee->userId()->value())) {
            $user = $this->userService->searchUserById(
                $this->userFactory->buildId($employee->userId()->value())
            );

            try {
                $user->state()->setValue($employee->state()->value());
                $dataUpdate['state'] = $user->state()->value();
                $this->userService->updateUser($user->id(), $dataUpdate);
                UserUpdateOrDeleteEvent::dispatch($user->id());
            } catch (Exception $exception) {
                $message = sprintf('User with ID:%d by employee with ID: %d can not be updated',
                    $user->id()->value(),
                    $employeeId->value()
                );
                $this->logger->error($message);
            }
        }

        return response()->json(status:Response::HTTP_CREATED);
    }

    public function getEmployee(null|int $id = null): JsonResponse|string
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($id);
        $employee = null;
        $user = null;

        if (!is_null($employeeId->value())) {
            $employee = $this->employeeService->searchEmployeeById($employeeId);
            $user = $this->userService->searchUserById($this->userFactory->buildId($employee->userId()->value()));
        }

        $profiles = $this->profileService->searchProfiles();
        $userId = (!is_null($employee)) ? $employee->userId()->value() : null;

        $view = view('employee.employee-form')
            ->with('userId', $userId)
            ->with('employeeId', $employeeId->value())
            ->with('employee', $employee)
            ->with('user', $user)
            ->with('profiles', $profiles)
            ->render();

        return $this->renderView($view);
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($request->employeeId);
        $userId = $this->userFactory->buildId($request->userId);

        try {
            $method = (is_null($employeeId->value())) ? 'createEmployee' : 'updateEmployee';
            $this->{$method}($request, $employeeId, $userId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return response()->json(['msg'=>'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(status:Response::HTTP_CREATED);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    private function prepareListEmployees(Employees $employees): JsonResponse
    {
        $dataEmployees = [];
        if ($employees->count()) {
            /**@var Employee $item*/
            foreach ($employees as $item) {
                $dataEmployees[] = $this->employeeDataTransformer->write($item)->readToShare();
            }
        }

        $datatable = $this->dataTable->collection(collect($dataEmployees));
        $datatable->addColumn('tools', function(array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * @throws Exception
     */
    #[NoReturn] private function updateEmployee(StoreEmployeeRequest $request, EmployeeId $employeeId, UserId $userId): void
    {
        $dataUpdate = [
            'identifier' => $request->identifier,
            'typeDocument' => $request->typeDocument,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'observations' => $request->observations,
            'birthdate' => DateTime::createFromFormat('d/m/Y',$request->birthdate)
        ];

        $this->employeeService->updateEmployee($employeeId, $dataUpdate);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return Middleware|array
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth','verify-session']),
            new Middleware('only.ajax-request', only:[
                'getEmployees'
            ]),
        ];
    }
}
