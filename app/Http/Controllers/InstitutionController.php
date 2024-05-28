<?php

namespace App\Http\Controllers;

use App\Http\Requests\Institution\StoreInstitutionRequest;
use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Exceptions\Exception as DatatablesException;

class InstitutionController extends Controller implements HasMiddleware
{
    use MultimediaTrait;

    private InstitutionFactoryContract $institutionFactory;
    private InstitutionDataTransformerContract $dataTransformer;
    private InstitutionManagementContract $institutionService;
    private DataTables $dataTable;
    private ViewFactory $viewFactory;

    public function __construct(
        InstitutionFactoryContract $institutionFactory,
        InstitutionDataTransformerContract $dataTransformer,
        InstitutionManagementContract $institutionService,
        DataTables $dataTable,
        ImageManager $imageManager,
        LoggerInterface $logger,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($logger);

        $this->setImageManager($imageManager);
        $this->institutionFactory = $institutionFactory;
        $this->dataTransformer = $dataTransformer;
        $this->institutionService = $institutionService;
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
        $institutions = $this->institutionService->searchInstitutions($request->input('filters'));

        return $this->prepareListInstitutions($institutions);
    }

    public function getInstitution(?int $id = null): JsonResponse|string
    {
        $institutionId = $this->institutionFactory->buildInstitutionId($id);

        $institution = null;
        if (!is_null($id)) {
            $institution = $this->institutionService->searchInstitutionById($institutionId);

            $urlFile = url(self::IMAGE_PATH_FULL.$institution->logo()->value().'?v='.Str::random(10));
        }

        $view = $this->viewFactory->make('institution.institution-form')
            ->with('institutionId', $institutionId->value())
            ->with('institution', $institution)
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
        $institutionId = $this->institutionFactory->buildInstitutionId($request->input('institutionId'));

        try {
            $method = (is_null($institutionId->value())) ? 'createInstitution' : 'updateInstitution';
            $this->{$method}($request, $institutionId);

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'ha ocurrido un error al guardar el registro, consulta con su administrador de sistema'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['institutionId' => $institutionId->value()], Response::HTTP_CREATED);
    }

    public function createInstitution(StoreInstitutionRequest $request, InstitutionId $id): void
    {
        $institution = $this->institutionFactory->buildInstitution(
            $id,
            $this->institutionFactory->buildInstitutionName($request->input('name'))
        );

        $institution->code()->setValue($request->input('code'));
        $institution->shortname()->setValue($request->input('shortname'));
        $institution->observations()->setValue($request->input('observations'));

        if (! is_null($request->input('token'))) {
            $filename = $this->saveImage($request->input('token'));
            $institution->logo()->setValue($filename);
        }

        try {
            $this->institutionService->createInstitution($institution);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }
    }

    public function updateInstitution(StoreInstitutionRequest $request, InstitutionId $id): void
    {
        $dataUpdate = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'shortname' => $request->input('shortname'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations')
        ];

        if (! is_null($request->input('token'))) {
            $filename = $this->saveImage($request->input('token'));
            $dataUpdate['logo'] = $filename;
        }

        $this->institutionService->updateInstitution($id, $dataUpdate);
    }

    public function deleteInstitution(int $id): JsonResponse
    {
        $institutionId = $this->institutionFactory->buildInstitutionId($id);

        try {
            $this->institutionService->deleteInstitution($institutionId);
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
