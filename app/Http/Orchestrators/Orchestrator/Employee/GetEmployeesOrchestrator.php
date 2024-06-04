<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 13:33:33
 */

namespace App\Http\Orchestrators\Orchestrator\Employee;

use App\Traits\DataTablesTrait;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception;

class GetEmployeesOrchestrator extends EmployeeOrchestrator
{
    use DataTablesTrait;

    private EmployeeDataTransformerContract $employeeDataTransformer;
    private DataTables $dataTables;
    private ViewFactory $viewFactory;

    public function __construct(
        EmployeeManagementContract $employeeManagement,
        EmployeeDataTransformerContract $employeeDataTransformer,
        DataTables $dataTables,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($employeeManagement);
        $this->setViewFactory($viewFactory);

        $this->employeeDataTransformer = $employeeDataTransformer;
        $this->dataTables = $dataTables;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function make(Request $request): JsonResponse
    {
        $employees = $this->employeeManagement->searchEmployees(
            $request->input('filters')
        );

        $dataEmployees = [];
        if ($employees->count()) {
            /** @var Employee $item */
            foreach ($employees as $item) {
                $dataEmployees[] = $this->employeeDataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataEmployees);
        $datatable = $this->dataTables->collection($collection);
        $datatable->addColumn('tools', function (array $element): string {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-employees';
    }
}
