<?php

namespace Core\Profile\Infrastructure\Commands;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class ProfileWarmup extends Command
{
    private ProfileRepositoryContract $readRepository;

    /** @var ProfileRepositoryContract[] */
    private array $repositories;
    private LoggerInterface $logger;
    private ProfileFactoryContract $profileFactory;

    public function __construct(
        LoggerInterface $logger,
        ProfileFactoryContract $profileFactory,
        ProfileRepositoryContract $readRepository,
        ProfileRepositoryContract ...$repositories,
    ) {

        parent::__construct();
        $this->logger = $logger;
        $this->profileFactory = $profileFactory;
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
    protected $signature = 'profile:warmup
                            {--id=0 : The ID Profile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup all of profile';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->option('id') == 0) {
            $profiles = $this->readRepository->getAll();

            foreach ($this->repositories as $repository) {
                foreach($profiles->aggregator() as $item) {
                    $profile = $this->readRepository->find($this->profileFactory->buildProfileId($item));
                    $repository->persistProfile($profile);
                }
            }
        } else {
            $profileId = $this->profileFactory->buildProfileId($this->option('id'));
            $profile = $this->readRepository->find($profileId);
            foreach ($this->repositories as $repository) {
                $repository->persistProfile($profile);
            }
        }

        $this->logger->info('Command executed');
    }
}
