<?php

namespace Core\Employee\Infrastructure\Commands;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class EmployeeWarmup extends Command
{
    private EmployeeFactoryContract $employeeFactory;
    private LoggerInterface $logger;

    /**@var EmployeeRepositoryContract[]  */
    private array $repositories;
    private EmployeeRepositoryContract $readRepository;

    public function __construct(
        LoggerInterface $logger,
        EmployeeFactoryContract $employeeFactory,
        EmployeeRepositoryContract $readRepository,
        EmployeeRepositoryContract ...$repositories,
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->employeeFactory = $employeeFactory;
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
    protected $signature = 'employee:warmup
                                {id : The ID employee}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup employee in memory';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($this->argument('id'));
        $employee = $this->readRepository->find($employeeId);
        foreach ($this->repositories as $repository) {
            $repository->persistEmployee($employee);
        }

        $this->logger->info('Employee command executed');
    }
}
