<?php

namespace Core\Employee\Infrastructure\Commands;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Exception;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class EmployeeWarmup extends Command
{
    /** @var EmployeeRepositoryContract[] */
    private array $repositories;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EmployeeFactoryContract $employeeFactory,
        private readonly EmployeeRepositoryContract $readRepository,
        EmployeeRepositoryContract ...$repositories,
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
    public function handle(): int
    {
        $id = ($this->argument('id')) ? (int) $this->argument('id') : null;
        $employeeId = $this->employeeFactory->buildEmployeeId($id);

        try {
            $employee = $this->readRepository->find($employeeId);

            foreach ($this->repositories as $repository) {

                if (! is_null($employee)) {
                    $repository->persistEmployee($employee);
                }
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return CommandSymfony::FAILURE;
        }

        $this->logger->info('Employee command executed');

        return CommandSymfony::SUCCESS;
    }
}
