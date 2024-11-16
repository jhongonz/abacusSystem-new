<?php

namespace Core\SharedContext\Infrastructure\Persistence;

use Core\Employee\Exceptions\SourceNotFoundException;
use Matrix\Enum\TaskStatus;
use Matrix\Task;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractChainRepository
{
    /** @var ChainPriority[] */
    private array $repositories;
    protected bool $canPersist = true;

    abstract public function functionNamePersist(): string;

    protected function canPersist(): bool
    {
        return $this->canPersist;
    }

    public function addRepository(ChainPriority $repository): self
    {
        $this->repositories[] = $repository;

        usort($this->repositories, $this->prioritySort());

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function write(string $functionName, mixed ...$source): mixed
    {
        $task = new Task(function () use ($functionName, $source) {
            $result = null;
            $repository = end($this->repositories);

            do {
                $callable = [$repository, $functionName];
                if (is_callable($callable)) {
                    $result = call_user_func_array($callable, $source);
                }
            } while (false !== ($repository = prev($this->repositories)));

            return $result;
        });

        $task->start();
        if (TaskStatus::FAILED === $task->getStatus()) {
            $task->retry();
        }

        return $task->getResult();
    }

    /**
     * @throws \Exception
     */
    protected function read(string $functionName, mixed ...$source): mixed
    {
        $result = $this->readFromRepositories($functionName, ...$source);

        if ($this->canPersist()) {
            $this->persistence($this->functionNamePersist(), $result);
        }

        return $result;
    }

    /**
     * @throws \Exception
     */
    protected function readFromRepositories(string $functionName, mixed ...$source): mixed
    {
        $task = new Task(function () use ($functionName, $source) {
            $result = null;
            $lastThrowable = new SourceNotFoundException('Source not found');

            $repository = reset($this->repositories);
            do {
                try {
                    $callable = [$repository, $functionName];
                    if (is_callable($callable)) {
                        $result = call_user_func_array($callable, $source);
                    }
                } catch (\Throwable $throwable) {
                    $lastThrowable = $throwable;
                }
            } while ((null === $result) and (false !== ($repository = next($this->repositories))));

            if (is_null($result)) {
                throw $lastThrowable;
            }

            return $result;
        });

        $task->start();
        if (TaskStatus::FAILED === $task->getStatus()) {
            $task->retry();
        }

        return $task->getResult();
    }

    protected function persistence(string $functionName, mixed ...$sources): void
    {
        while (false !== ($repository = prev($this->repositories))) {
            try {
                $callable = [$repository, $functionName];
                if (is_callable($callable)) {
                    call_user_func_array($callable, $sources);
                }
            } catch (\Exception $exception) {
            }
        }
    }

    private function prioritySort(): \Closure
    {
        return static function (ChainPriority $current, ChainPriority $next) {
            if ($current->priority() === $next->priority()) {
                return 0;
            }

            return ($current->priority() < $next->priority()) ? 1 : -1;
        };
    }
}
