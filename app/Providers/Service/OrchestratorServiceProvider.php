<?php

namespace App\Providers\Service;

use App\Http\Orchestrators\OrchestratorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Illuminate\Contracts\Config\Repository as Configuration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class OrchestratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(OrchestratorHandlerContract::class, function (Application $app) {
            return new OrchestratorHandler();
        });
    }

    /**
     * Bootstrap services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $configuration = $this->app->make(Configuration::class);

        /** @var array<string> $orchestrators */
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
}
