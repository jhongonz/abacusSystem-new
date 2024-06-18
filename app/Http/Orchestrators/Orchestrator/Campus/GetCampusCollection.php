<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-18 09:52:41
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use App\Traits\DataTablesTrait;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception;

class GetCampusCollection extends CampusOrchestrator
{
    use DataTablesTrait;

    private CampusDataTransformerContract $campusDataTransformer;
    private DataTables $dataTables;
    private ViewFactory $viewFactory;

    public function __construct(
        CampusManagementContract $campusManagement,
        CampusDataTransformerContract $campusDataTransformer,
        DataTables $dataTables,
        ViewFactory $viewFactory
    ) {
        parent::__construct($campusManagement);
        $this->setViewFactory($viewFactory);

        $this->campusDataTransformer = $campusDataTransformer;
        $this->dataTables = $dataTables;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function make(Request $request): JsonResponse
    {
        $campusCollection = $this->campusManagement->searchCampusCollection(
            $request->input('institutionId'),
            $request->input('filters')
        );

        $dataCampus = [];
        if ($campusCollection->count()) {
            /** @var Campus $item */
            foreach ($campusCollection as $item) {
                $dataCampus[] = $this->campusDataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataCampus);
        $dataTable = $this->dataTables->collection($collection);
        $dataTable->addColumn('tools', function (array $element): string {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $dataTable->escapeColumns([])->toJson();
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-campus-collection';
    }
}
