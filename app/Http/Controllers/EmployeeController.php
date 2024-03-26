<?php

namespace App\Http\Controllers;

use Core\Employee\Application\Factory\EmployeeFactory;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Psr\Log\LoggerInterface;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    private EmployeeManagementContract $employeeService;
    private EmployeeFactory $employeeFactory;
    private EmployeeDataTransformerContract $employeeDataTransformer;
    private DataTables $dataTable;
    public function __construct(
        EmployeeManagementContract $employeeService,
        EmployeeFactory $employeeFactory,
        EmployeeDataTransformerContract $employeeDataTransformer,
        DataTables $dataTable,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->employeeService = $employeeService;
        $this->employeeFactory = $employeeFactory;
        $this->employeeDataTransformer = $employeeDataTransformer;
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
