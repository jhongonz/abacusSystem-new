<?php

namespace Core\Profile\Infrastructure\Commands;

use Core\Profile\Domain\Contracts\ProfileFactoryContract;
use Core\Profile\Domain\Contracts\ProfileRepositoryContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class ProfileWarmup extends Command
{
    /** @var ProfileRepositoryContract[] */
    private array $repositories;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ProfileFactoryContract $profileFactory,
        private readonly ProfileRepositoryContract $readRepository,
        ProfileRepositoryContract ...$repositories,
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
    protected $signature = 'profile:warmup {--id=0 : The ID Profile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup profiles in memory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = is_numeric($this->option('id')) ? intval($this->option('id')) : null;

        if (0 == $id) {
            /** @var Profiles $profiles */
            $profiles = $this->readRepository->getAll();

            foreach ($this->repositories as $repository) {
                foreach ($profiles->aggregator() as $item) {
                    try {
                        /** @var Profile $profile */
                        $profile = $this->readRepository->find($this->profileFactory->buildProfileId($item));

                        $repository->persistProfile($profile);
                    } catch (\Exception $exception) {
                        $this->logger->error($exception->getMessage(), $exception->getTrace());

                        return CommandSymfony::FAILURE;
                    }
                }
            }
        } else {
            $profileId = $this->profileFactory->buildProfileId($id);

            foreach ($this->repositories as $repository) {
                try {
                    /** @var Profile $profile */
                    $profile = $this->readRepository->find($profileId);

                    $repository->persistProfile($profile);
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage(), $exception->getTrace());

                    return CommandSymfony::FAILURE;
                }
            }
        }

        $this->logger->info('Command executed');

        return CommandSymfony::SUCCESS;
    }
}
