<?php

namespace App\Http\Controllers;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Employee\Domain\Employee;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;

class CampusController extends Controller
{
    private OrchestratorHandlerContract $orchestrators;
    private Session $session;

    public function __construct(
        OrchestratorHandlerContract $orchestrators,
        Session $session,
        LoggerInterface $logger,
        ViewFactory $viewFactory
    ) {
        parent::__construct($logger, $viewFactory);
        $this->session = $session;
        $this->orchestrators = $orchestrators;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('campus.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function getCampus(Request $request): JsonResponse
    {
        /** @var Employee $employee */
        $employee = $this->session->get('employee');

        $request->merge(['institutionId' => $employee->institutionId()->value()]);
        return $this->orchestrators->handler('retrieve-campus-collection', $request);
    }
}
