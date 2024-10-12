<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:11:30
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Traits\DataTablesTrait;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\Factory as ViewFactory;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception;

class GetInstitutionsOrchestrator extends InstitutionOrchestrator
{
    use DataTablesTrait;

    public function __construct(
        InstitutionManagementContract $institutionManagement,
        private readonly InstitutionDataTransformerContract $institutionDataTransformer,
        private readonly DataTables $dataTables,
        protected ViewFactory $viewFactory
    ) {
        parent::__construct($institutionManagement);
        $this->setViewFactory($viewFactory);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function make(Request $request): JsonResponse
    {
        $institutions = $this->institutionManagement->searchInstitutions(
            $request->input('filters')
        );

        $dataInstitutions = [];
        if ($institutions->count()) {
            /** @var Institution $item */
            foreach ($institutions as $item) {
                $dataInstitutions[] = $this->institutionDataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataInstitutions);
        $datatable = $this->dataTables->collection($collection);
        $datatable->addColumn('tools', function (array $element) {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-institutions';
    }
}
