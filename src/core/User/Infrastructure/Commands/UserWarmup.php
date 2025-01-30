<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Commands;

use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Core\User\Domain\User;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class UserWarmup extends Command
{
    /** @var UserRepositoryContract[] */
    private array $repositories;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UserFactoryContract $userFactory,
        private readonly UserRepositoryContract $readRepository,
        UserRepositoryContract ...$repositories,
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
    protected $signature = 'user:warmup
                            {id : The ID user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup user in memory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = (is_numeric($this->argument('id'))) ? intval($this->argument('id')) : null;
        $userId = $this->userFactory->buildId($id);

        try {
            /** @var User $user */
            $user = $this->readRepository->find($userId);

            foreach ($this->repositories as $repository) {
                $repository->persistUser($user);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return CommandSymfony::FAILURE;
        }

        $this->logger->info('User command executed');

        return CommandSymfony::SUCCESS;
    }
}
