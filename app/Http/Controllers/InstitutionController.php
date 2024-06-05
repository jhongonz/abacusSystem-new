<?php

namespace App\Http\Controllers;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Institution\StoreInstitutionRequest;
use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Institution;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class InstitutionController extends Controller implements HasMiddleware
{
    use MultimediaTrait;

    private OrchestratorHandlerContract $orchestratorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        ImageManagerInterface $imageManager,
        LoggerInterface $logger,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($logger, $viewFactory);

        $this->setImageManager($imageManager);
        $this->orchestratorHandler = $orchestratorHandler;
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
        try {
            $this->orchestratorHandler->handler('change-state-institution', $request);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    public function getInstitutions(Request $request): JsonResponse
    {
        return $this->orchestratorHandler->handler('retrieve-institutions', $request);
    }

    public function getInstitution(Request $request, ?int $id = null): JsonResponse|string
    {
        $request->mergeIfMissing(['institutionId' => $id]);
        $dataInstitution = $this->orchestratorHandler->handler('detail-institution', $request);

        $view = $this->viewFactory->make('institution.institution-form', $dataInstitution)
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
        try {
            $method = (is_null($request->input('institutionId'))) ? 'createInstitution' : 'updateInstitution';

            /** @var Institution $institution */
            $institution = $this->{$method}($request);

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'ha ocurrido un error al guardar el registro, consulta con su administrador de sistema'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse([
            'institutionId' => $institution->id()->value()
        ], Response::HTTP_CREATED);
    }

    public function createInstitution(StoreInstitutionRequest $request): Institution
    {
        return $this->orchestratorHandler->handler('create-institution', $request);
    }

    public function updateInstitution(StoreInstitutionRequest $request): Institution
    {
        return $this->orchestratorHandler->handler('update-institution', $request);
    }

    public function deleteInstitution(Request $request, int $id): JsonResponse
    {
        $request->mergeIfMissing(['institutionId' => $id]);

        try {
            $this->orchestratorHandler->handler('delete-institution', $request);
        } catch (Exception $exception) {
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
