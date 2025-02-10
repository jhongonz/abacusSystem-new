<?php

namespace App\Http\Controllers;

use App\Events\EventDispatcher;
use App\Events\Institution\InstitutionUpdateOrDeletedEvent;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Institution\StoreInstitutionRequest;
use App\Traits\DataTablesTrait;
use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class InstitutionController extends Controller implements HasMiddleware
{
    use MultimediaTrait;
    use DataTablesTrait;

    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly DataTables $dataTables,
        protected readonly EventDispatcher $dispatcher,
        protected ImageManagerInterface $imageManager,
        protected ViewFactory $viewFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('institution.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws \Exception
     */
    public function changeStateInstitution(Request $request): JsonResponse
    {
        try {
            $this->orchestrators->handler('change-state-institution', $request);

            $this->dispatcher->dispatch(new InstitutionUpdateOrDeletedEvent($request->integer('institutionId')));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function getInstitutions(Request $request): JsonResponse
    {
        $dataInstitutions = $this->orchestrators->handler('retrieve-institutions', $request);

        $datatable = $this->dataTables->collection($dataInstitutions);
        $datatable->addColumn('tools', function (array $element) {
            return $this->retrieveMenuOptionHtml($element);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public function getInstitution(Request $request, ?int $id = null): JsonResponse|string
    {
        $request->merge(['institutionId' => $id]);

        /** @var array<int|string, mixed> $dataInstitution */
        $dataInstitution = $this->orchestrators->handler('detail-institution', $request);

        $view = $this->viewFactory->make('institution.institution-form', $dataInstitution)
            ->render();

        return $this->renderView($view);
    }

    public function setLogoInstitution(Request $request): JsonResponse
    {
        $uploadedFile = $request->file('file');
        if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
            $random = Str::random();
            $imageUrl = $this->saveImageTmp($uploadedFile->getRealPath(), $random);

            return new JsonResponse(['token' => $random, 'url' => $imageUrl], Response::HTTP_CREATED);
        }

        return new JsonResponse(['msg' => 'Could not upload file.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function storeInstitution(StoreInstitutionRequest $request): JsonResponse
    {
        try {
            $method = (is_null($request->input('institutionId'))) ? 'create-institution' : 'update-institution';

            /** @var array{institution: Institution} $dataInstitution */
            $dataInstitution = $this->orchestrators->handler($method, $request);
            $institution = $dataInstitution['institution'];

            $this->dispatcher->dispatch(new InstitutionUpdateOrDeletedEvent($request->integer('institutionId')));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'ha ocurrido un error al guardar el registro, consulta con su administrador de sistema'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse([
            'institutionId' => $institution->id()->value(),
        ], Response::HTTP_CREATED);
    }

    public function deleteInstitution(Request $request, int $id): JsonResponse
    {
        $request->merge(['institutionId' => $id]);

        try {
            $this->orchestrators->handler('delete-institution', $request);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verify-session']),
        ];
    }
}
