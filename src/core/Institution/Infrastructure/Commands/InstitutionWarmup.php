<?php

namespace Core\Institution\Infrastructure\Commands;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Exception;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class InstitutionWarmup extends Command
{
    private InstitutionFactoryContract $institutionFactory;
    private LoggerInterface $logger;

    /** @var InstitutionRepositoryContract[] */
    private array $repositories;

    private InstitutionRepositoryContract $readRepository;

    public function __construct(
        LoggerInterface $logger,
        InstitutionFactoryContract $institutionFactory,
        InstitutionRepositoryContract $readRepository,
        InstitutionRepositoryContract ...$repositories,
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->institutionFactory = $institutionFactory;
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
    protected $signature = 'institution:warmup
                                {id: The ID institution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup institution in memory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $institutionId = $this->institutionFactory->buildInstitutionId($this->argument('id'));

        try {
            $institution = $this->readRepository->find($institutionId);

            foreach ($this->repositories as $repository) {
                $repository->persistInstitution($institution);
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return CommandSymfony::FAILURE;
        }

        $this->logger->info('Institution command executed');

        return CommandSymfony::SUCCESS;
    }
}
