<?php

namespace Core\Profile\Infrastructure\Commands;

use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleRepositoryContract;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class ModuleWarmup extends Command
{
    private LoggerInterface $logger;
    private ModuleFactoryContract $moduleFactory;
    private ModuleRepositoryContract $readRepository;

    /** @var ModuleRepositoryContract[] */
    private array $repositories;

    public function __construct(
        LoggerInterface $logger,
        ModuleFactoryContract $moduleFactory,
        ModuleRepositoryContract $readRepository,
        ModuleRepositoryContract ...$repositories,
    ){
        parent::__construct();
        $this->logger = $logger;
        $this->moduleFactory = $moduleFactory;
        $this->readRepository = $readRepository;

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
    public function handle(): void
    {
        if ($this->option('id') == 0) {
            $modules = $this->readRepository->getAll();

            foreach($this->repositories as $repository) {
                foreach ($modules->aggregator() as $item) {
                    $module = $this->readRepository->find($this->moduleFactory->buildModuleId($item));
                    $repository->persistModule($module);
                }
            }
        } else {
            $moduleId = $this->moduleFactory->buildModuleId($this->option('id'));
            $module = $this->readRepository->find($moduleId);
            foreach ($this->repositories as $repository) {
                $repository->persistModule($module);
            }
        }

        $this->logger->info('Command executed');
    }
}
