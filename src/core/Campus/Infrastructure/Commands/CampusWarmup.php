<?php

namespace Core\Campus\Infrastructure\Commands;

use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;
use Exception;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class CampusWarmup extends Command
{
    /** @var CampusRepositoryContract[] */
    private array $repositories;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CampusFactoryContract $campusFactory,
        private readonly CampusRepositoryContract $readRepository,
        CampusRepositoryContract ...$repositories
    ) {
        $this->repositories = $repositories;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campus:warmup
                                {id : The ID campus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup campus in memory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $campusId = $this->campusFactory->buildCampusId($this->argument('id'));

        try {
            $campus = $this->readRepository->find($campusId);

            foreach ($this->repositories as $repository) {
                $repository->persistCampus($campus);
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return CommandSymfony::FAILURE;
        }
        $this->logger->info('Campus command executed');

        return CommandSymfony::SUCCESS;
    }
}
