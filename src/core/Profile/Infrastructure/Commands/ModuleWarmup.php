<?php

namespace Core\Profile\Infrastructure\Commands;

use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Exception;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class ModuleWarmup extends Command
{
    /** @var ModuleRepositoryContract[] */
    private array $repositories;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ModuleFactoryContract $moduleFactory,
        private readonly ModuleRepositoryContract $readRepository,
        ModuleRepositoryContract ...$repositories,
    ) {
        parent::__construct();

        foreach ($repositories as $repository) {
            $this->repositories[] = $repository;
        }
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:warmup
                            {--id=0 : The ID module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup modules in memory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = ($this->option('id')) ? (int) $this->option('id') : null;
        if ($id == 0) {
            $modules = $this->readRepository->getAll();

            foreach ($this->repositories as $repository) {
                foreach ($modules->aggregator() as $item) {

                    try {
                        $module = $this->readRepository->find($this->moduleFactory->buildModuleId($item));

                        $repository->persistModule($module);
                    } catch (Exception $exception) {
                        $this->logger->error($exception->getMessage(), $exception->getTrace());

                        return CommandSymfony::FAILURE;
                    }
                }
            }
        } else {

            $moduleId = $this->moduleFactory->buildModuleId($id);
            foreach ($this->repositories as $repository) {
                try {
                    $module = $this->readRepository->find($moduleId);

                    $repository->persistModule($module);
                } catch (Exception $exception) {
                    $this->logger->error($exception->getMessage(), $exception->getTrace());

                    return CommandSymfony::FAILURE;
                }
            }
        }

        $this->logger->info('Command executed');
        return CommandSymfony::SUCCESS;
    }
}
