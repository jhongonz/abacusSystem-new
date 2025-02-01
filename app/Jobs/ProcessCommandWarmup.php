<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class ProcessCommandWarmup extends CommandWarmup
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * @param string|array<string> $command
     */
    public function __construct(
        private readonly string|array $command
    ) {
        parent::__construct();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (is_array($this->command)) {
                /** @var string $command */
                foreach ($this->command as $command) {
                    $this->artisan->call($command);
                }
            } else {
                $this->artisan->call($this->command);
            }
        } catch (CommandNotFoundException $exception) {
            $this->fail($exception);
        }
    }
}
