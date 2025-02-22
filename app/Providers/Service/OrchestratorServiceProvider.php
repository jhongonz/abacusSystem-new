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
     *
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $configuration = $this->app->make(Configuration::class);

        /** @var array<int, string> $orchestrators */
        $orchestrators = $configuration->get('orchestrators');

        $this->app->tag($orchestrators, 'orchestrators');
    }

    /**
     * Bootstrap services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $handler = $this->app->make(OrchestratorHandlerContract::class);

        foreach ($this->app->tagged('orchestrators') as $orchestrator) {
            $handler->addOrchestrator($orchestrator);
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
