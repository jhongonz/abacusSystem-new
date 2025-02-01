<?php

namespace App\Providers\Service;

use App\Http\Orchestrators\OrchestratorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Illuminate\Contracts\Config\Repository as Configuration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class OrchestratorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        OrchestratorHandlerContract::class => OrchestratorHandler::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $configuration = $this->app->make(Configuration::class);

        /** @var array<int, string> $orchestrators */
        $orchestrators = $configuration->get('orchestrators');

        $handler = $this->app->make(OrchestratorHandlerContract::class);
        foreach ($orchestrators as $orchestrator) {
            if (class_exists($orchestrator)) {
                $handler->addOrchestrator($this->app->make($orchestrator));
            } else {
                throw new \InvalidArgumentException(sprintf('The orchestrator class %s does not exists.', $orchestrator));
            }
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            OrchestratorHandlerContract::class,
        ];
    }
}
