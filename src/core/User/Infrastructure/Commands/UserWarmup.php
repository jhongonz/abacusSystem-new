<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Commands;

use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\Contracts\UserRepositoryContract;
use Exception;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as CommandSymfony;

class UserWarmup extends Command
{
    private LoggerInterface $logger;

    private UserFactoryContract $userFactory;

    /** @var UserRepositoryContract[] */
    private array $repositories;

    private UserRepositoryContract $readRepository;

    public function __construct(
        LoggerInterface $logger,
        UserFactoryContract $userFactory,
        UserRepositoryContract $readRepository,
        UserRepositoryContract ...$repositories,
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->userFactory = $userFactory;
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
        $userId = $this->userFactory->buildId($this->argument('id'));

        try {
            $user = $this->readRepository->find($userId);

            foreach ($this->repositories as $repository) {
                $repository->persistUser($user);
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return CommandSymfony::FAILURE;
        }

        $this->logger->info('User command executed');

        return CommandSymfony::SUCCESS;
    }
}
