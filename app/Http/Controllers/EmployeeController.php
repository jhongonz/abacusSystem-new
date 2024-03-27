<?php

namespace App\Http\Controllers;

use Core\Employee\Application\Factory\EmployeeFactory;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserManagementContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    private EmployeeManagementContract $employeeService;
    private EmployeeFactory $employeeFactory;
    private EmployeeDataTransformerContract $employeeDataTransformer;
    private UserFactoryContract $userFactory;
    private UserManagementContract $userService;
    private DataTables $dataTable;
    public function __construct(
        EmployeeManagementContract $employeeService,
        EmployeeFactory $employeeFactory,
        EmployeeDataTransformerContract $employeeDataTransformer,
        UserFactoryContract $userFactory,
        UserManagementContract $userService,
        DataTables $dataTable,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->employeeService = $employeeService;
        $this->employeeFactory = $employeeFactory;
        $this->employeeDataTransformer = $employeeDataTransformer;
        $this->userFactory = $userFactory;
        $this->userService = $userService;
        $this->dataTable = $dataTable;
    }

    public function index(): JsonResponse|string
    {
        $view = view('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

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
     * Get the middleware that should be assigned to the controller.
     *
     * @return Middleware|array
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware('auth'),
            new Middleware('only.ajax-request', only:[
                'getEmployees',
            ]),
        ];
    }
}
