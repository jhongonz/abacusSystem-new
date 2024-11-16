<?php

namespace Core\Institution\Infrastructure\Commands;

use Core\Institution\Domain\Contracts\InstitutionFactoryContract;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Exception;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class InstitutionWarmup extends Command
{
    /** @var InstitutionRepositoryContract[] */
    private array $repositories;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly InstitutionFactoryContract $institutionFactory,
        private readonly InstitutionRepositoryContract $readRepository,
        InstitutionRepositoryContract ...$repositories,
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
        $id = ($this->argument('id')) ? (int) $this->argument('id') : null;
        $institutionId = $this->institutionFactory->buildInstitutionId($id);

        try {
            $institution = $this->readRepository->find($institutionId);

            foreach ($this->repositories as $repository) {

                if (! is_null($institution)) {
                    $repository->persistInstitution($institution);
                }
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return CommandSymfony::FAILURE;
        }

        $this->logger->info('Institution command executed');

        return CommandSymfony::SUCCESS;
    }
}
