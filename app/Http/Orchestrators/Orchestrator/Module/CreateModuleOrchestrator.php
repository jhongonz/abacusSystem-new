<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:53:22
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Exceptions\RouteNotFoundException;
use App\Traits\RouterTrait;
use Assert\AssertionFailedException;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class CreateModuleOrchestrator extends ModuleOrchestrator
{
    use RouterTrait;

    public function __construct(
        ModuleManagementContract $moduleManagement,
        protected Router $router,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct($moduleManagement);
        $this->setRouter($router);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RouteNotFoundException
     */
    public function make(Request $request): array
    {
        $route = $request->string('route');

        try {
            $this->validateRoute($route);
        } catch (AssertionFailedException $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new RouteNotFoundException(sprintf('Route <%s> not found', $route), Response::HTTP_NOT_FOUND);
        }

        $dataModule = [
            'id' => null,
            'key' => $request->input('key'),
            'name' => $request->input('name'),
            'route' => $route,
            'icon' => $request->input('icon'),
            'position' => $request->input('position'),
            'state' => ValueObjectStatus::STATE_NEW,
        ];

        $module = $this->moduleManagement->createModule([Module::TYPE => $dataModule]);

        return ['module' => $module];
    }

    public function canOrchestrate(): string
    {
        return 'create-module';
    }
}
