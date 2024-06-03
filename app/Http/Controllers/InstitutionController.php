<?php

namespace App\Http\Controllers;

use App\Http\Requests\Institution\StoreInstitutionRequest;
use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception as DatatablesException;

class InstitutionController extends Controller implements HasMiddleware
{
    use MultimediaTrait;
    private InstitutionDataTransformerContract $dataTransformer;
    private InstitutionManagementContract $institutionService;
    private DataTables $dataTable;

    public function __construct(
        InstitutionDataTransformerContract $dataTransformer,
        InstitutionManagementContract $institutionService,
        DataTables $dataTable,
        ImageManagerInterface $imageManager,
        LoggerInterface $logger,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($logger, $viewFactory);

        $this->setImageManager($imageManager);
        $this->dataTransformer = $dataTransformer;
        $this->institutionService = $institutionService;
        $this->dataTable = $dataTable;
    }
    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('institution.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws Exception
     */
    public function changeStateInstitution(Request $request): JsonResponse
    {
        $institution = $this->institutionService->searchInstitutionById($request->input('id'));

        $institutionState = $institution->state();
        if ($institutionState->isNew() || $institutionState->isInactivated()) {
            $institutionState->activate();
        } elseif ($institutionState->isActivated()) {
            $institutionState->inactive();
        }

        $dataUpdate['state'] = $institutionState->value();
        $dataUpdate['updatedAt'] = $this->getDateTime();

        try {
            $this->institutionService->updateInstitution($request->input('id'), $dataUpdate);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    /**
     * @throws DatatablesException
     */
    public function getInstitutions(Request $request): JsonResponse
    {
        $institutions = $this->institutionService->searchInstitutions($request->input('filters'));

        return $this->prepareListInstitutions($institutions);
    }

    public function getInstitution(?int $id = null): JsonResponse|string
    {
        if (!is_null($id)) {
            $institution = $this->institutionService->searchInstitutionById($id);
            $urlFile = url(self::IMAGE_PATH_FULL.$institution->logo()->value().'?v='.Str::random(10));
        }

        $view = $this->viewFactory->make('institution.institution-form')
            ->with('institutionId', $id)
            ->with('institution', $institution ?? null)
            ->with('image', $urlFile ?? null)
            ->render();

        return $this->renderView($view);
    }

    public function setLogoInstitution(Request $request): JsonResponse
    {
        $random = Str::random(10);
        $imageUrl = $this->saveImageTmp($request->file('file')->getRealPath(), $random);

        return new JsonResponse(['token' => $random, 'url' => $imageUrl], Response::HTTP_CREATED);
    }

    public function storeInstitution(StoreInstitutionRequest $request): JsonResponse
    {
        $institutionId = $request->input('institutionId');

        try {
            $method = (is_null($institutionId)) ? 'createInstitution' : 'updateInstitution';
            $this->{$method}($request, $institutionId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'ha ocurrido un error al guardar el registro, consulta con su administrador de sistema'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['institutionId' => $institutionId], Response::HTTP_CREATED);
    }

    /**
     * @throws Exception
     */
    public function createInstitution(StoreInstitutionRequest $request, ?int $id): Institution
    {
        $data = [
            'id' => $id,
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'shortname' => $request->input('shortname'),
            'observations' => $request->input('observations'),
            'createdAt' => $this->getDateTime(),
        ];

        $token = $request->input('token');
        if (! is_null($token)) {
            $filename = $this->saveImage($token);
            $data['logo'] = $filename;
        }

        return $this->institutionService->createInstitution([Institution::TYPE => $data]);
    }

    /**
     * @throws Exception
     */
    public function updateInstitution(StoreInstitutionRequest $request, int $id): void
    {
        $dataUpdate = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'shortname' => $request->input('shortname'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
            'updatedAt' => $this->getDateTime(),
        ];

        $token = $request->input('token');
        if (! is_null($token)) {
            $filename = $this->saveImage($token);
            $dataUpdate['logo'] = $filename;
        }

        $this->institutionService->updateInstitution($id, $dataUpdate);
    }

    public function deleteInstitution(int $id): JsonResponse
    {
        try {
            $this->institutionService->deleteInstitution($id);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_OK);
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
                $dataInstitutions[] = $this->dataTransformer->write($item)->readToShare();
            }
        }

        $collection = new Collection($dataInstitutions);
        $datatable = $this->dataTable->collection($collection);
        $datatable->addColumn('tools', function (array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * Get the middleware that should be assigned to the controller.
     * @codeCoverageIgnore
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verify-session']),
        ];
    }
}
