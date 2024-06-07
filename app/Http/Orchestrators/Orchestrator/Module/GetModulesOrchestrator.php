<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 18:13:48
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use App\Traits\DataTablesTrait;
use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception;

class GetModulesOrchestrator extends ModuleOrchestrator
{
    use DataTablesTrait;

    private ModuleDataTransformerContract $moduleDataTransformer;
    private ViewFactory $viewFactory;
    private DataTables $dataTables;

    public function __construct(
        ModuleManagementContract $moduleManagement,
        ModuleDataTransformerContract $moduleDataTransformer,
        DataTables $dataTables,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($moduleManagement);
        $this->setViewFactory($viewFactory);

        $this->dataTables = $dataTables;
        $this->moduleDataTransformer = $moduleDataTransformer;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function make(Request $request): JsonResponse
    {
        $modules = $this->moduleManagement->searchModules(
            $request->input('filters')
        );

        $dataModules = [];
        if ($modules->count()) {
            /** @var Module $item */
            foreach ($modules as $item) {
                $dataModules[] = $this->moduleDataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataModules);
        $datatable = $this->dataTables->collection($collection);
        $datatable->addColumn('tools', function (array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-modules';
    }
}
