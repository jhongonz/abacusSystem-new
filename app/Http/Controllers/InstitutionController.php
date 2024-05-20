<?php

namespace App\Http\Controllers;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;

class InstitutionController extends Controller implements HasMiddleware
{
    private InstitutionFactoryContract $institutionFactory;
    private ViewFactory $viewFactory;

    public function __construct(
        InstitutionFactoryContract $institutionFactory,
        LoggerInterface $logger,
        ViewFactory $viewFactory,
    ) {
        parent::__construct($logger);
        $this->institutionFactory = $institutionFactory;
        $this->viewFactory = $viewFactory;
    }
    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('employee.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verify-session']),
        ];
    }
}
