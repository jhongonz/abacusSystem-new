<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-24 12:14:54
 */

namespace App\Http\Controllers;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;

class TeacherController extends Controller
{
    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly ActionExecutorHandler $actionExecutorHandler,
        LoggerInterface $logger,
        ViewFactory $viewFactory
    ) {
        parent::__construct($logger, $viewFactory);
    }
}
