<?php

namespace App\Http\Controllers;

use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception as DatatablesException;

class InstitutionController extends Controller implements HasMiddleware
{
    private InstitutionFactoryContract $institutionFactory;
    private InstitutionDataTransformerContract $dataTransformer;
    private InstitutionManagementContract $institutionManagement;
    private DataTables $dataTable;
    private ViewFactory $viewFactory;

    public function __construct(
        InstitutionFactoryContract $institutionFactory,
        InstitutionDataTransformerContract $dataTransformer,
        InstitutionManagementContract $institutionManagement,
        DataTables $dataTable,
        LoggerInterface $logger,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($logger);

        $this->institutionFactory = $institutionFactory;
        $this->dataTransformer = $dataTransformer;
        $this->institutionManagement = $institutionManagement;
        $this->dataTable = $dataTable;
        $this->viewFactory = $viewFactory;
    }
    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('institution.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws DatatablesException
     */
    public function getInstitutions(Request $request): JsonResponse
    {
        $institutions = $this->institutionManagement->searchInstitutions($request->input('filters'));

        return $this->prepareListInstitutions($institutions);
    }

    public function getInstitution(?int $id = null): JsonResponse|string
    {
        $institutionId = $this->institutionFactory->buildInstitutionId($id);

        $institution = null;
        if (!is_null($id)) {
            $institution = $this->institutionManagement->searchInstitutionById($institutionId);
        }

        $view = $this->viewFactory->make('institution.institution-form')
            ->with('institutionId', $institutionId->value())
            ->with('institution', $institution)
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws DatatablesException
     */
    private function prepareListInstitutions(Institutions $institutions): JsonResponse
    {
        $dataInstitutions = [];
        if ($institutions->count()) {
            /** @var Institution $item */
            foreach ($institutions as $item) {
                $dataInstitutions[] = $this->dataTransformer->write($item)->read();
            }
        }

        $datatable = $this->dataTable->collection(collect($dataInstitutions));
        $datatable->addColumn('tools', function (array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verify-session']),
        ];
    }
}
